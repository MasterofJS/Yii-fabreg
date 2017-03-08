<?php
namespace common\behaviors;
use Closure;
use common\interfaces\EntityInterface;
use common\interfaces\Notifiable;
use common\models\ActiveRecord;
use common\models\Notification;
use common\models\User;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\AfterSaveEvent;

/**
 * Class NotificationBehavior
 * @package common\behaviors
 *
 * @property-read EntityInterface entity
 * @property-read User $actor
 * @property-read User $previousActor
 * @property-read User receiver
 *
 */
class NotificationBehavior extends Behavior
{

    public $type;

    /**
     * @var string
     */
    public $entityAttribute;

    /**
     * @var string
     */
    public $receiverAttribute;

    /**
     * @var string
     */
    public $actorAttribute;


    /**
     * @var mixed
     */
    public $entityValue;

    /**
     * @var mixed
     */
    public $receiverValue;

    /**
     * @var string
     */
    public $actorValue;


    public $notificationClass;


    public function init()
    {
        parent::init();

        if (null === $this->type) {
            throw new InvalidConfigException('"type" property must be specified.');
        }

        if (null === $this->entityAttribute  && null === $this->entityValue) {
            throw new InvalidConfigException('Either "entityAttribute" or "entityValue" property must be specified.');
        }

        if (null === $this->receiverAttribute && null === $this->receiverValue) {
            throw new InvalidConfigException('Either "receiverAttribute" or "receiverValue" property must be specified.');
        }

        if (null === $this->actorAttribute && null === $this->actorValue) {
            throw new InvalidConfigException('Either "userAttribute" or "userValue" property must be specified.');
        }
    }


    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'notify',
        ];
    }

    /**
     * @param AfterSaveEvent $event
     */
    public function notify($event)
    {
        /** @var Notifiable $sender */
        $sender = $event->sender;
        if( ($sender instanceof Notifiable) && $sender->notify()){
            if(null === $this->notificationClass){
                $this->notificationClass = Notification::className();
            }
            $notification = \Yii::createObject($this->notificationClass);
            if($notification instanceof Notification){
                $notification->entity_id = $this->entity->getId();
                $notification->entity_type = $this->entity->getType();
                $notification->actor_id = $this->actor->id;
                $notification->receiver_id = $this->receiver->id;
                $notification->type = $this->type;
                if($this->receiver->allowsNotification($notification)){
                    $tr = Notification::getDb()->beginTransaction();
                    try {
                        Notification::deleteAll($notification->getAttributes(['entity_id', 'entity_type', 'receiver_id', 'type']));
                        $notification->last_actor_id = $sender->getLastActorId();
                        $notification->last_count = $sender->getLastCount();
                        $notification->save();
                        $tr->commit();
                    } catch (\Exception $e) {
                        $tr->rollBack();
                    }
                }
            }
        }
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        if($this->entityAttribute){
            return $this->owner[$this->entityAttribute];
        }else{
            if ($this->entityValue instanceof Closure || is_array($this->entityValue) && is_callable($this->entityValue)) {
                return call_user_func($this->entityValue, $this->owner);
            }

            return $this->entityValue;
        }
    }

    /**
     * @return User
     */
    public function getActor()
    {
        if($this->actorAttribute){
            return $this->owner[$this->actorAttribute];
        }else {
            if ($this->actorValue instanceof Closure || is_array($this->actorValue) && is_callable($this->actorValue)) {
                return call_user_func($this->actorValue, $this->owner);
            }

            return $this->actorValue;
        }
    }


    /**
     * @return user
     */
    public function getReceiver()
    {
        if($this->receiverAttribute){
            return $this->owner[$this->receiverAttribute];
        }else {
            if ($this->receiverValue instanceof Closure || is_array($this->receiverValue) && is_callable($this->receiverValue)) {
                return call_user_func($this->receiverValue, $this->owner);
            }
            return $this->receiverValue;
        }
    }
}