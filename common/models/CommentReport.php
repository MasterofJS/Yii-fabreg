<?php

namespace common\models;

use console\migrations\Migration;
use Yii;

/**
 * This is the model class for table "comment_report".
 *
 * @property string $user_id
 * @property string $comment_id
 *
 * @see m160302_164914_comment_report_table
 */
class CommentReport extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_COMMENT_REPORT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'comment_id'], 'required'],
            [['user_id', 'comment_id'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function beforeValidate()
    {
        if(empty($this->user_id)){
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }
}
