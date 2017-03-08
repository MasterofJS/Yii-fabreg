<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 3/6/16
 * Time: 1:52 PM
 */

namespace common\models;

use common\helpers\FileHelper;
use yii\helpers\Url;

class Cover extends BaseMedia
{
    const TYPE = 3;

    const LOCALE_BASE_PATH = '@frontend/web/media/cover';
    const LOCALE_BASE_URL = '@public/media/cover';

    public function getUrl($scheme = true)
    {
        if ($this->is_uploaded_to_cdn) {
            return \Yii::$app->cloudStorage->getPublicUrl($this->getPath());
        }
        return Url::to(self::LOCALE_BASE_URL . '/' . $this->getBaseName(), $scheme);
    }

    public function getPath()
    {
        return \Yii::getAlias(self::LOCALE_BASE_PATH . DIRECTORY_SEPARATOR . $this->getBaseName());
    }

    public function handle($tempFileName)
    {
        parent::handle($tempFileName);
        FileHelper::move(FileHelper::tmpPath($tempFileName), $this->getPath());
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