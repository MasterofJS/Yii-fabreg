<?php

namespace common\models;

use console\migrations\Migration;
use Yii;

/**
 * This is the model class for table "post_report".
 *
 * @property string $user_id
 * @property string $post_id
 * @property int $status
 * @property int $type
 *
 * @property-read User $user
 * @property-read Post $post
 *
 * @see m160302_164848_post_report_table
 */
class PostReport extends ActiveRecord
{
    const TYPE_COPYRIGHT = 0;
    const TYPE_SPAM = 1;
    const TYPE_OFFENSIVE = 2;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DISAPPROVED = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_POST_REPORT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'post_id'], 'required'],
            [['user_id', 'post_id', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['type', 'in', 'range' => [self::TYPE_COPYRIGHT, self::TYPE_SPAM, self::TYPE_OFFENSIVE]],
            ['type', 'default', 'value' => self::TYPE_COPYRIGHT],
            ['status', 'in', 'range' => [self::STATUS_APPROVED, self::STATUS_DISAPPROVED, self::STATUS_PENDING]],
            ['status', 'default', 'value' => self::STATUS_PENDING],
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->user_id)){
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function isPending()
    {
        return self::STATUS_PENDING == $this->status;
    }
}
