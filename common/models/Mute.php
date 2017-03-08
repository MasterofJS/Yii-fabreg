<?php

namespace common\models;

use console\migrations\Migration;
use Yii;

/**
 * This is the model class for table "user_mute".
 *
 * @property string $receiver_id
 * @property string $sender_id
 *
 * @see m160302_164948_user_mute_table
 */
class Mute extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_USER_MUTE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiver_id', 'sender_id'], 'required'],
            [['receiver_id', 'sender_id'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->receiver_id)){
            $this->receiver_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }
}
