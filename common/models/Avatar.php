<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 3/6/16
 * Time: 1:34 PM
 */

namespace common\models;

use common\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class Avatar extends BaseMedia implements Linkable
{
    const TYPE = 2;

    const LOCALE_BASE_PATH = '@frontend/web/media/avatar';
    const LOCALE_BASE_URL = '@public/media/avatar';
    const LOCALE_DEFAULT_BASE_PATH = '@frontend/web/media/default-avatar';
    const LOCALE_DEFAULT_BASE_URL = '@public/media/default-avatar';
    const MAX_WIDTH = 155;

    public static function random()
    {
        return static::find()->defaults()->select(['id'])->orderBy('RAND()')->limit(1)->scalar();
    }

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => $this->getUrl()
        ];
    }

    public function getUrl($scheme = true)
    {
        if ($this->is_uploaded_to_cdn) {
            return \Yii::$app->cloudStorage->getPublicUrl($this->getPath());
        }
        if ($this->is_default) {
            return Url::to(self::LOCALE_DEFAULT_BASE_URL . '/' . $this->getBaseName(), $scheme);
        }
        return Url::to(self::LOCALE_BASE_URL . '/' . $this->getBaseName(), $scheme);
    }

    public function getPath()
    {
        if (!$this->is_default) {
            return \Yii::getAlias(self::LOCALE_BASE_PATH . DIRECTORY_SEPARATOR . $this->getBaseName());
        }
        return \Yii::getAlias(self::LOCALE_DEFAULT_BASE_PATH . DIRECTORY_SEPARATOR . $this->getBaseName());
    }

    public function handle($tempFileName)
    {
        parent::handle($tempFileName);
        FileHelper::move(FileHelper::tmpPath($tempFileName), $this->getPath());
        FileHelper::thumbnail($this->getPath(), Avatar::MAX_WIDTH);
        if (!$this->update()) {
            $this->throwValidationException();
        }
    }

    public function beforeSave($insert)
    {
        $this->target = self::TYPE;
        return parent::beforeSave($insert);
    }

    public function getFiles()
    {
        return [$this->getPath()];
    }
}
