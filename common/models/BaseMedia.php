<?php

namespace common\models;

use console\migrations\Migration;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "image".
 *
 * @property string $id
 * @property string $name
 * @property string $extension
 * @property string $type
 * @property integer $size
 * @property bool $is_default
 * @property bool $is_over_max_height
 * @property bool $is_uploaded_to_cdn
 * @property int $target
 * @see m160224_110000_image_table
 */
class BaseMedia extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_IMAGE;
    }

    public function getBaseName($suffix = null, $extension = null)
    {
        $fileName = $this->name;
        if ($suffix) {
            $fileName .= $suffix;
        }
        if (null === $extension) {
            $extension = $this->extension;
        }
        $fileName .= '.' . $extension;
        return $fileName;
    }

    /**
     * @param string $secureName
     * @return static
     */
    public static function findByName($secureName)
    {
        return static::findOne(['name' => $secureName]);
    }

    /**
     * @param string $id
     * @return static
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'extension', 'type', 'size'], 'required'],
            [['size', 'target'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 5],
            [['type'], 'string', 'max' => 31],
            [['name'], 'unique'],
            [['is_default', 'is_over_max_height', 'is_uploaded_to_cdn'], 'boolean'],
            [['is_default', 'is_over_max_height', 'is_uploaded_to_cdn'], 'default', 'value' => false],
        ];
    }

    /**
     * @return ImageQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(ImageQuery::className(), [get_called_class()]);
    }

    public function getUrl()
    {
        return null;
    }

    public function getPath()
    {
        return null;
    }

    /**
     * @param $tempFileName
     * @see ImageUploadForm::upload()
     */
    public function handle($tempFileName)
    {
        // remove the code below, if you are not saving temp files in cdn
        \Yii::$app->cloudStorage->downloadTmp($tempFileName);
    }

    public function getIsGif()
    {
        return !strcmp('image/gif', $this->type);
    }

    public function getIsLong()
    {
        return true == $this->is_over_max_height;
    }

    public function setIsLong($isLong)
    {
        $this->is_over_max_height = $isLong;
    }

    public static function instantiate($row)
    {
        switch ($row['target']) {
            case Avatar::TYPE:
                return new Avatar();
            case Cover::TYPE:
                return new Cover();
            case Media::TYPE:
                return new Media();
            default:
                return new self;
        }
    }

    public function getFiles()
    {
        return [];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('is_uploaded_to_cdn', $changedAttributes)) {
            if ($this->is_uploaded_to_cdn) {
                Task::deleteFromLocale($this->getFiles());
            }
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // remove the code below, if you are not saving media files instantly
        if (!$insert && !$this->is_uploaded_to_cdn) {
            $this->instantUploadToCDN();
            $this->is_uploaded_to_cdn = true;
        }
        return true;
    }


    public function afterDelete()
    {
        if ($this->is_uploaded_to_cdn) {
            Task::deleteFromCDN($this->getFiles());
        } else {
            Task::deleteFromLocale($this->getFiles());
        }
        parent::afterDelete();
    }

    public function instantUploadToCDN()
    {
        foreach ($this->getFiles() as $file) {
            Yii::$app->cloudStorage->upload($file);
        }
    }
}

class ImageQuery extends ActiveQuery
{

    public function defaults()
    {
        return $this->andWhere(['is_default' => true])->orderBy('name ASC');
    }

    public function local()
    {
        return $this->andWhere(['is_uploaded_to_cdn' => false]);
    }
}
