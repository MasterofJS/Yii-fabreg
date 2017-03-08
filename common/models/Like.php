<?php

namespace common\models;

use common\behaviors\NotificationBehavior;
use common\interfaces\EntityInterface;
use common\interfaces\Notifiable;
use console\migrations\Migration;
use Yii;

/**
 * This is the model class for table "like".
 *
 * @property string $id
 * @property string $user_id
 * @property string $entity_id
 * @property integer $entity_type
 * @property integer $value
 *
 * @property-read EntityInterface $entity
 * @property-read User $user
 *
 * @see m160225_092506_like_table
 */
class Like extends ActiveRecord implements Notifiable
{
    private $_entity;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => NotificationBehavior::className(),
                'type' => Notification::TYPE_LIKE,
                'entityValue' => function($model){
                    /** @var static $model */
                    return $model->entity;
                },
                'receiverValue' => function($model){
                    /** @var static $model */
                    return User::findIdentity($model->entity->authorId);
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_LIKE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'entity_id'], 'required'],
            [['user_id', 'entity_id', 'value', 'entity_type'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->user_id)){
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    /**
     * @param string $entityClass
     * @return int
     */
    public static function getEntityType($entityClass)
    {
        $entity = Yii::createObject($entityClass);
        if($entity instanceof Post){
            return Post::TYPE;
        }elseif($entity instanceof Comment){
            return Comment::TYPE;
        }else{
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param $entityId
     * @param $entityClass
     * @param $value
     * @param null $userId
     * @return static
     */
    public static function getInstance($entityId, $entityClass, $value, $userId = null)
    {
        if(null === $userId){
            $userId = Yii::$app->user->id;
        }
        $attributes = [
            'entity_id' => $entityId,
            'entity_type' => self::getEntityType($entityClass),
            'user_id' => $userId,
        ];
        $record = static::findOne($attributes);

        if(null === $record) {
            $record = new static;
            $record->setAttributes($attributes);
            $record->value = $value;
        }else{
            if($record->value != $value){
                $record->value = $value;
            }else{
                $record->value = 0;
            }
        }
        return $record;
    }

    public function notify()
    {
        return $this->value == 1;
    }

    public function getLastActorId()
    {
        $id =  static::find()
            ->andWhere(['<', 'updated_at', $this->updated_at])
            ->select(['user_id'])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $this->entity->getAuthorId()])
            ->andWhere(['entity_type' => $this->entity_type, 'entity_id' => $this->entity_id, 'value' => $this->value])
            ->orderBy(['updated_at' => SORT_DESC])
            ->scalar();
        return $id === false ? null : $id;
    }

    public function getLastCount()
    {
        return static::find()
            ->andWhere(['<', 'updated_at', $this->updated_at])
            ->andWhere(['<>', 'user_id', $this->user_id])
            ->andWhere(['<>', 'user_id', $this->entity->getAuthorId()])
            ->andWhere(['entity_type' => $this->entity_type, 'entity_id' => $this->entity_id, 'value' => $this->value])
            ->count();
    }
}
