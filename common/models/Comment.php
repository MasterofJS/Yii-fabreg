<?php

namespace common\models;

use common\behaviors\NotificationBehavior;
use common\interfaces\EntityInterface;
use common\interfaces\Notifiable;
use console\migrations\Migration;
use Yii;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * This is the model class for table "comment".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $reply_id
 * @property string $user_id
 * @property string $post_id
 * @property string $content
 * @property integer $type
 * @property int $comments
 * @property int $likes
 * @property int $dislikes
 * @property bool $liked
 * @property bool $disliked
 * @property User $author
 * @property Post $post
 * @property bool $canViewerReport
 * @property bool $canViewerDelete
 * @property Comment repliedComment
 *
 * @see m160225_084618_comment_table
 */
class   Comment extends ActiveRecord implements EntityInterface, Notifiable
{
    const STATUS_ACTIVE = 6;
    const STATUS_BANNED = 0;

    const TYPE = 1;

    const TYPE_TEXT = 0;
    const TYPE_MEDIA = 1;

    const REL_COMMENTS = 'comments';

    private $_likes;
    private $_dislikes;
    private $_liked;
    private $_disliked;
    private $_comments;

    private $_can_viewer_report;
    private $_can_viewer_delete;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => NotificationBehavior::className(),
                'type' => Notification::TYPE_COMMENT,
                'entityValue' => function($model){
                    /** @var static $model */
                    return $model->reply_id == null ? $model->post : $model->repliedComment;
                },
                'receiverValue' => function($model){
                    /** @var static $model */
                    return User::findIdentity($model->reply_id == null ? $model->post->authorId : $model->repliedComment->authorId);
                },
                'actorValue' => function($model){
                    /** @var static $model */
                    return $model->author;
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_COMMENT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'reply_id', 'user_id', 'post_id', 'type'], 'integer'],
            [['user_id', 'post_id', 'content'], 'required'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['content'], 'string', 'max' => 500],
            ['type', 'default', 'value' => self::TYPE_TEXT],
            ['type', 'in', 'range' => [self::TYPE_TEXT, self::TYPE_MEDIA]]
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->user_id)){
            $this->user_id = Yii::$app->user->id;
        }

        if(!empty($this->reply_id)){
            $comment = static::findOne($this->reply_id);
            $this->parent_id = $comment ? ( $comment->parent_id ? : $comment->id ) : null;
        }

        return parent::beforeValidate();
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getRepliedComment()
    {
        return $this->hasOne(static::className(), ['id' => 'reply_id']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function getUrl($scheme = true)
    {
        return $this->post->getUrl($scheme, 'c' . $this->id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return self::TYPE;
    }

    public function notify()
    {
        return true;
    }

    public function getAuthorId()
    {
        return $this->user_id;
    }

    public function getLastActorId()
    {
        $receiver_id = $this->reply_id == null ? $this->post->getAuthorId() : $this->repliedComment->getAuthorId();
        $id = static::find()
            ->andWhere(['<', 'updated_at', $this->updated_at])
            ->andWhere(['post_id' => $this->post_id, 'reply_id' => $this->reply_id])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $receiver_id])
            ->select(['user_id'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->scalar();
        return $id === false ? null : $id;
    }

    public function getLastCount()
    {
        $receiver_id = $this->reply_id == null ? $this->post->getAuthorId() : $this->repliedComment->getAuthorId();
        return static::find()->andWhere(['<', 'updated_at', $this->updated_at])
            ->andWhere(['post_id' => $this->post_id, 'reply_id' => $this->reply_id])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $receiver_id])
            ->count("DISTINCT user_id");
    }

    /**
     * @return int
     */
    public function getComments()
    {
        if ($this->isNewRecord || $this->parent_id != null) {
            return null;
        }
        if ($this->_comments === null) {
            $this->setComments(Comment::find()->andWhere(['parent_id' => $this->id])->count());
        }
        return $this->_comments;
    }

    /**
     * @param int $comments
     */
    public function setComments($comments)
    {
        $this->_comments = (int) $comments;
    }

    /**
     * @return bool
     */
    public function getLiked()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_liked === null) {
            if(Yii::$app->user->isGuest){
                $this->setLiked(false);
            }else{
                $this->setLiked(Like::find()->select(['value'])->andWhere(['entity_id' => $this->id, 'entity_type' => self::TYPE, 'user_id' => Yii::$app->user->id])->scalar());
            }
        }
        return $this->_liked;
    }

    /**
     * @param bool $liked
     */
    public function setLiked($liked)
    {
        if($liked == -1){
            $this->_disliked = true;
            $this->_liked = false;
        }elseif($liked == 1){
            $this->_disliked = false;
            $this->_liked = true;
        }elseif($liked === false){
            $this->_disliked = false;
            $this->_liked = false;
        }else{
            $this->_liked = false;
        }
    }

    /**
     * @return bool
     */
    public function getDisliked()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_disliked === null) {
            if(Yii::$app->user->isGuest){
                $this->setDisliked(false);
            }else{
                $this->setDisliked(Like::find()->select(['value'])->andWhere(['entity_id' => $this->id, 'entity_type' => self::TYPE, 'user_id' => Yii::$app->user->id])->scalar());
            }
        }
        return $this->_disliked;
    }

    /**
     * @param mixed $disliked
     */
    public function setDisliked($disliked)
    {
        if($disliked == -1){
            $this->_disliked = true;
            $this->_liked = false;
        }elseif($disliked == 1){
            $this->_disliked = false;
            $this->_liked = true;
        }elseif($disliked === false){
            $this->_disliked = false;
            $this->_liked = false;
        }else{
            $this->_disliked = false;
        }
    }

    /**
     * @return int
     */
    public function getDislikes()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_dislikes === null) {
            $this->setDislikes(Like::find()->andWhere(['entity_id' => $this->id, 'entity_type' => self::TYPE, 'value' => -1])->count());
        }
        return $this->_dislikes;
    }

    /**
     * @param int $dislikes
     */
    public function setDislikes($dislikes)
    {
        $this->_dislikes = (int) $dislikes;
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        if ($this->isNewRecord) {
            return null;
        }

        if ($this->_likes === null) {
            $this->setLikes(Like::find()->andWhere(['entity_id' => $this->id, 'entity_type' => self::TYPE, 'value' => 1])->count());
        }

        return $this->_likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes($likes)
    {
        $this->_likes = (int) $likes;
    }

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return [
            self::REL_COMMENTS => $this->getCommentsUrl()
        ];
    }

    public function getCommentsUrl($scheme = true)
    {
        return Url::toRoute(['comment/index', 'id' => $this->id], $scheme);
    }

    /**
     * @return bool
     */
    public function getCanViewerReport()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_can_viewer_report === null) {
            if(Yii::$app->user->isGuest || $this->user_id == Yii::$app->user->id){
                $this->setCanViewerReport(false);
            }else{
                $this->setCanViewerReport(!CommentReport::find()->andWhere(['comment_id' => $this->id, 'user_id' => Yii::$app->user->id])->exists());
            }
        }
        return $this->_can_viewer_report;
    }

    /**
     * @param bool $can_viewer_report
     */
    public function setCanViewerReport($can_viewer_report)
    {
        $this->_can_viewer_report = !!$can_viewer_report;
    }

    /**
     * @return bool
     */
    public function getCanViewerDelete()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_can_viewer_delete === null) {
            $this->setCanViewerDelete($this->user_id == Yii::$app->user->id);
        }
        return $this->_can_viewer_delete;
    }

    /**
     * @param bool $can_viewer_delete
     */
    public function setCanViewerDelete($can_viewer_delete)
    {
        $this->_can_viewer_delete = $can_viewer_delete;
    }

    public static function getOrder($filter, $default = SORT_ASC)
    {
        switch($filter){
            case 'hot':
                return ['likes' => SORT_DESC];
            default:
                return ['created_at' => $default];
        }
    }


}
