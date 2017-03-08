<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/13/16
 * Time: 3:45 PM
 */

namespace common\models;

use common\helpers\FileHelper;
use console\migrations\Migration;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "task".
 *
 * @property string $id
 * @property array $data
 * @property string $action
 * @property array $options
 *
 * @see m160413_123940_task_table
 */
class Task extends ActiveRecord
{
    const ACTION_UPLOAD_TO_CDN = 'upload.to.cdn';
    const ACTION_DELETE_FROM_CDN = 'delete.from.cdn';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_TASK;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'action'], 'required'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['data', 'options'], 'string'],
            [['action'], 'string', 'max' => 31]
        ];
    }


    public static function create($data, $action, $options = null)
    {
        $new = new static([
            'data' => $data,
            'action' => $action,
            'options' => $options,
        ]);
        $new->save();
        return $new->errors;
    }

    public static function deleteFromCDN($data, $options = null)
    {
        return self::create($data, self::ACTION_DELETE_FROM_CDN, $options);
    }

    public static function deleteFromLocale($files)
    {
        foreach ($files as $file) {
            FileHelper::unlink($file);
        }
    }

    public static function uploadToCDN($data, $options = null)
    {
        return self::create($data, self::ACTION_UPLOAD_TO_CDN, $options);
    }

    public function afterFind()
    {
        parent::afterFind();

        if (empty($this->data)) {
            $this->data = [];
        } elseif (is_string($this->data)) {
            $this->data = Json::decode($this->data);
        }

        if (empty($this->options)) {
            $this->options = [];
        } elseif (is_string($this->options)) {
            $this->options = Json::decode($this->options);
        }
    }

    public function beforeValidate()
    {
        if (empty($this->data)) {
            $this->data = null;
        } elseif (is_array($this->data)) {
            $this->data = Json::encode($this->data);
        }

        if (empty($this->options)) {
            $this->options = null;
        } elseif (is_array($this->options)) {
            $this->options = Json::encode($this->options);
        }

        return parent::beforeValidate();
    }

    /**
     * @return TaskQuery
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function resolve($key)
    {
        $data = $this->data;
        unset($data[$key]);
        $this->data = $data;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if (empty($this->data) && !$this->isNewRecord) {
            return $this->delete();
        }
        return parent::save($runValidation, $attributeNames);
    }
}

class TaskQuery extends ActiveQuery
{

    public function deleteFromCDN()
    {
        return $this->andWhere(['action' => Task::ACTION_DELETE_FROM_CDN]);
    }

    public function uploadFromCDN()
    {
        return $this->andWhere(['action' => Task::ACTION_UPLOAD_TO_CDN]);
    }
}
