<?php

namespace common\models;

use common\behaviors\NotificationBehavior;
use common\interfaces\Notifiable;
use console\migrations\Migration;
use Yii;

/**
 * This is the model class for table "share".
 *
 * @property string $user_id
 * @property string $post_id
 * @property integer $network
 *
 * @property-read User $user
 * @property-read Post $post
 *
 * @see m160314_131402_share_table
 */
class Share extends ActiveRecord implements Notifiable
{
    const NETWORK_FACEBOOK = 1;
    const NETWORK_GOOGLE = 2;
    const NETWORK_TWITTER = 3;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => NotificationBehavior::className(),
                'type' => Notification::TYPE_SHARE,
                'entityValue' => function($model){
                    /** @var static $model */
                    return $model->post;
                },
                'receiverValue' => function($model){
                    /** @var static $model */
                    return $model->post->author;
                },
                'actorValue' => function($model){
                    /** @var static $model */
                    return $model->user;
                }
            ]
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_SHARE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'post_id', 'network'], 'required'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['user_id', 'post_id', 'network'], 'integer'],
            [['network'], 'in', 'range' => [self::NETWORK_FACEBOOK, self::NETWORK_GOOGLE, self::NETWORK_TWITTER]]
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->user_id)){
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public function notify()
    {
        return true;
    }

    public function getLastActorId()
    {
        $id = static::find()
            ->select(['user_id'])
            ->andWhere(['<', 'updated_at', $this->updated_at])
            ->andWhere(['post_id' => $this->post_id])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $this->post->user_id])
            ->orderBy(['updated_at' => SORT_DESC])
            ->scalar();
        return $id === false ? null : $id;
    }

    public function getLastCount()
    {
        return static::find()
            ->andWhere(['<', 'updated_at', $this->updated_at])
            ->andWhere(['post_id' => $this->post_id])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $this->post->user_id])
            ->count("DISTINCT user_id");
    }
}
