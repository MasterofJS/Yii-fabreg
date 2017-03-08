<?php

namespace common\models;

use common\interfaces\EntityInterface;
use console\migrations\Migration;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;
use yii\web\Linkable;

/**
 * This is the model class for table "post".
 *
 * @property string $id
 * @property string $user_id
 * @property string $description
 * @property string $description2
 * @property string $photo_id
 * @property bool $is_nsfw
 * @property integer $status
 * @property integer $channel
 * @property string $released_at
 * @property bool $is_retired
 * @property int $likes
 * @property int $dislikes
 * @property int $comments
 * @property bool $liked
 * @property bool $disliked
 * @property bool $canViewerReport
 * @property bool $canViewerDelete
 * @property bool $isFeatured
 * @property bool $isNotification
 * @property integer $showDesc
 * @property-read Media $photo
 * @property-read User $author
 * @property-read string $hashId
 * @see m160225_084549_post_table
 */
class Post extends ActiveRecord implements Linkable, EntityInterface
{
    const TYPE = 0;

    const STATUS_ACTIVE = 6;
    const STATUS_BANNED = 0;

    const CHANNEL_FRESH = 0;
    const CHANNEL_PENDING_TRENDING = 1;
    const CHANNEL_TRENDING = 2;
    const CHANNEL_PENDING_HOT = 3;
    const CHANNEL_HOT = 4;

    const REL_PHOTO = 'photo';
    const REL_VIDEO = 'video';
    const REL_COMMENTS = 'comments';

    protected static $publicIdSalt = 'awdrgyjilzscfbhm';

    private $_likes;
    private $_dislikes;
    private $_liked;
    private $_disliked;
    private $_comments;

    private $_can_viewer_report;
    private $_can_viewer_delete;

    private $_isFeatured;
    private $_isNotification;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_POST;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'description', 'photo_id'], 'required'],
            [['user_id', 'photo_id', 'showDesc'], 'integer'],
            [['created_at', 'updated_at', 'released_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
						[['description2'], 'string'],
            [['description'], 'string', 'max' => 255],
            [['is_nsfw', 'is_retired'], 'boolean'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BANNED]],
            [['channel'], 'in', 'range' => [self::CHANNEL_FRESH, self::CHANNEL_PENDING_TRENDING, self::CHANNEL_TRENDING, self::CHANNEL_PENDING_HOT, self::CHANNEL_HOT]],
        ];
    }

    public function getHashId()
    {
        return static::encodeId($this->id);
    }

    public function getPhoto()
    {
        return $this->hasOne(Media::className(), ['id' => 'photo_id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->released_at = $this->created_at;
            }
            return true;
        }
        return false;
    }

    public function beforeValidate()
    {
        if (empty($this->user_id)) {
            $this->user_id = Yii::$app->user->id;
        }

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('photo_id', $changedAttributes)) {
            if (!empty($changedAttributes['photo_id'])) {
                $photo = Media::findById($changedAttributes['photo_id']);
                if ($photo) {
                    $photo->delete();
                }
            }

            if (!empty($this->photo_id)) {
                $photo = $this->photo;
                $tempFileName = $photo->getBaseName();
                $photo->name = static::encodeId($this->id);
                $photo->handle($tempFileName);
            }
        }

        if (array_key_exists('channel', $changedAttributes)) {
            if (self::CHANNEL_TRENDING == $this->channel) {
                Notification::trendingPromotion($this, $this->user_id);
            } elseif (self::CHANNEL_HOT == $this->channel) {
                Notification::hotPromotion($this, $this->user_id);
            }
        }

        if(array_key_exists('status', $changedAttributes)){
            if(self::STATUS_BANNED == $this->status){
                $this->ban(false);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function setPhoto($secureName)
    {
        if (!$secureName) {
            return;
        }
        $session = Yii::$app->session;
        $session->open();
        $imageInfo = $session->get($secureName);
        $session->close();
        if (null !== $imageInfo) {
            $image = new Media();
            $image->setAttributes($imageInfo);
            if (!$image->save()) {
                $image->throwValidationException();
            }
            $this->photo_id = $image->id;
        }
    }

    public function afterDelete()
    {
        if ($this->photo) {
            $this->photo->delete();
        }
        parent::afterDelete();
    }

    /**
     * f%&)mc+gi7Bb
     * @param $id
     * @return static
     */
    public static function findByHashId($id)
    {
        return static::findOne(['id' => static::decodeId($id)]);
    }


    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        $links = [
            self::REL_PHOTO => $this->getPhotoUrl(),
        ];

        if ($this->photo->getIsLong()) {
            $links = array_merge($links, [
                Media::TYPE_LI => $this->photo->getUrl(true, Media::TYPE_LI)
            ]);
        }

        if ($this->photo->getIsGif()) {
            $links = array_merge($links, [
                self::REL_VIDEO => [
                    Media::TYPE_GIF => $this->photo->getUrl(true, Media::TYPE_GIF),
                    Media::TYPE_MP4 => $this->photo->getUrl(true, Media::TYPE_MP4),
                    Media::TYPE_WEBM => $this->photo->getUrl(true, Media::TYPE_WEBM),
                ],
            ]);
        }

        $links[self::REL_COMMENTS] = $this->getCommentsUrl();
        return $links;
    }

    public function getCommentsUrl($scheme = true)
    {
        return Url::toRoute(['post/comments', 'id' => static::encodeId($this->id)], $scheme);
    }

    public function getPhotoUrl($scheme = true)
    {
        if($this->isFeatured){
            return $this->photo->getUrl(true, Media::TYPE_FI);
        }elseif($this->isNotification){
            return $this->photo->getUrl(true, Media::TYPE_NI);
        }elseif($this->photo->getIsGif()){
            return $this->photo->getUrl(true, Media::TYPE_PI);
        }elseif($this->photo->getIsLong()){
            return $this->photo->getUrl(true, Media::TYPE_CI);
        }
        return $this->photo->getUrl($scheme);
    }


    public function getUrl($scheme = true, $hash = null)
    {
        return Url::toRoute(['post/view', 'id' => static::encodeId($this->id), '#' => $hash], $scheme);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return self::TYPE;
    }

    public function getAuthorId()
    {
        return $this->user_id;
    }

    /**
     * @return PostQuery
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    }

    /**
     * @return int
     */
    public function getComments()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_comments === null) {
            $this->setComments(Comment::find()->andWhere(['post_id' => $this->id])->count());
        }
        return $this->_comments;
    }

    /**
     * @param int $comments
     */
    public function setComments($comments)
    {
        $this->_comments = (int)$comments;
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
            if (Yii::$app->user->isGuest) {
                $this->setLiked(false);
            } else {
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
        if ($liked == -1) {
            $this->_disliked = true;
            $this->_liked = false;
        } elseif ($liked == 1) {
            $this->_disliked = false;
            $this->_liked = true;
        } elseif ($liked === false) {
            $this->_disliked = false;
            $this->_liked = false;
        } else {
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
            if (Yii::$app->user->isGuest) {
                $this->setDisliked(false);
            } else {
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
        if ($disliked == -1) {
            $this->_disliked = true;
            $this->_liked = false;
        } elseif ($disliked == 1) {
            $this->_disliked = false;
            $this->_liked = true;
        } elseif ($disliked === false) {
            $this->_disliked = false;
            $this->_liked = false;
        } else {
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
        $this->_dislikes = (int)$dislikes;
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
        $this->_likes = (int)$likes;
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
            if (Yii::$app->user->isGuest || $this->user_id == Yii::$app->user->id) {
                $this->setCanViewerReport(false);
            } else {
                $this->setCanViewerReport(!PostReport::find()->andWhere(['post_id' => $this->id, 'user_id' => Yii::$app->user->id])->exists());
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

    public function getChannelText()
    {
        $channel = null;
        switch ($this->channel) {
            case self::CHANNEL_FRESH:
                $channel = 'fresh';
                break;
            case self::CHANNEL_PENDING_TRENDING:
                $channel = 'trending up';
                break;
            case self::CHANNEL_TRENDING:
                $channel = 'trending';
                break;
            case self::CHANNEL_PENDING_HOT:
                $channel = 'hot up';
                break;
            case self::CHANNEL_HOT:
                $channel = 'hot';
                break;
        }
        return $channel;
    }

    public static function getChannelFromText($text)
    {
        $channel = null;
        switch ($text) {
            case 'fresh':
                $channel = self::CHANNEL_FRESH;
                break;
            case 'trending':
                $channel = self::CHANNEL_TRENDING;
                break;
            case 'hot':
                $channel = self::CHANNEL_HOT;
                break;
        }
        return $channel;
    }

    /**
     * @return mixed
     */
    public function getIsFeatured()
    {
        if(null === $this->_isFeatured){
            $this->_isFeatured = false;
        }
        return $this->_isFeatured;
    }

    public function getIsNotification()
    {
        if(null === $this->_isNotification){
            $this->_isNotification = false;
        }
        return $this->_isNotification;
    }

    /**
     * @param mixed $featured
     */
    public function setIsFeatured($featured)
    {
        $this->_isFeatured = !!$featured;
    }

    public function setIsNotification($notification)
    {
        $this->_isNotification = !!$notification;
    }

    public function ban($update = true)
    {
        if(self::STATUS_BANNED != $this->status ){
            $this->status = self::STATUS_BANNED;
            if(!$update || ($update && $this->update())){
                \Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'post-report'],
                        ['user' => $this->author, 'post' => $this]
                    )
                    ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
                    ->setTo($this->author->email)
                    ->setSubject('Seu post foi denunciado e bloqueado.')
                    ->send();
                return true;
            }
        }
        return false;
    }

}

class PostQuery extends ActiveQuery
{
    public function fresh()
    {
        return $this->andWhere(['channel' => Post::CHANNEL_FRESH]);
    }

    public function trending()
    {
        return $this->andWhere(['channel' => Post::CHANNEL_TRENDING]);
    }

    public function hot()
    {
        return $this->andWhere(['channel' => Post::CHANNEL_HOT]);
    }

    public function active($channel)
    {
        return $this->andWhere(['is_retired' => false, 'channel' => Post::getChannelFromText($channel)]);
    }

    //release candidate
    public function rc()
    {
        return $this->andWhere(['channel' => [Post::CHANNEL_PENDING_TRENDING, Post::CHANNEL_PENDING_HOT]])->andWhere(['<=', 'released_at', Post::createDateTime()]);
    }

    public function retired($state)
    {
        return $this->andWhere(['is_retired' => $state]);
    }

    public function olderThan($delay)
    {
        return $this->andWhere('(released_at + INTERVAL :delay HOUR) <= :now')->addParams(['delay' => $delay, 'now' => Post::createDateTime()]);
    }

    public function channel($channel)
    {
        return $this->andWhere(['>=', 'channel', Post::getChannelFromText($channel)]);
    }

    public function featured()
    {
        return $this->orderBy('RAND()');
    }

    public function match($query)
    {
        $words = preg_split('/\P{L}+/u', $query, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (empty($words)) {
            return $this->andWhere(['description' => $query]);
        }
        foreach ($words as $word) {
            $this->andWhere(['REGEXP ', 'description', '[[:<:]]' . $word . '[[:>:]]']);
        }
        return $this;
    }
}
