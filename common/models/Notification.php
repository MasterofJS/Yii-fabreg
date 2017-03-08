<?php

namespace common\models;

use common\interfaces\EntityInterface;
use console\migrations\Migration;
use Yii;
use yii\web\Link;
use yii\web\Linkable;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property string $actor_id
 * @property string $last_actor_id
 * @property string $receiver_id
 * @property string $entity_id
 * @property integer $entity_type
 * @property integer $type
 * @property integer $is_read
 * @property integer $last_count
 *
 *
 * @property-read EntityInterface $entity
 * @see m160302_164816_notification_table
 */
class Notification extends ActiveRecord implements Linkable
{
    const TYPE_LIKE = 0;
    const TYPE_COMMENT = 1;
    const TYPE_SHARE = 2;
    const TYPE_POINTS = 3;
    const TYPE_COMMENTS = 4;
    const TYPE_TRENDING = 5;
    const TYPE_HOT = 6;

    private $_entity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_NOTIFICATION;
    }

    /**
     * @param EntityInterface $entity
     * @param string $receiverId
     * @return bool
     */
    public static function trendingPromotion($entity, $receiverId)
    {
        return static::system(self::TYPE_TRENDING, $entity, $receiverId);
    }

    /**
     * @param EntityInterface $entity
     * @param string $receiverId
     * @return bool
     */
    public static function hotPromotion($entity, $receiverId)
    {
        return static::system(self::TYPE_HOT, $entity, $receiverId);
    }

    /**
     * @param int $type
     * @param EntityInterface $entity
     * @param string $receiverId
     * @return bool
     */
    protected static function system($type, $entity, $receiverId)
    {
        $notification = new static;
        $notification->entity_id = $entity->getId();
        $notification->entity_type = $entity->getType();
        $notification->receiver_id = $receiverId;
        $notification->type = $type;
        return $notification->save();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['actor_id', 'last_actor_id', 'receiver_id', 'entity_id', 'entity_type', 'type', 'is_read'], 'integer'],
            [['receiver_id', 'entity_id'], 'required'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => $this->getUrl(),
        ];
    }

    public function getUrl($scheme = true)
    {
        return $this->entity->getUrl($scheme);
    }

    public function getEntity()
    {
        if(null === $this->_entity){
            switch($this->entity_type){
                case Post::TYPE:
                    $this->_entity =  Post::findOne(['id' => $this->entity_id]);
                    break;
                case Comment::TYPE:
                    $this->_entity =  Comment::findOne(['id' => $this->entity_id]);
                    break;
                default:
                    $this->_entity =  null;
            }
        }
        return $this->_entity;
    }
}
