<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/13/16
 * Time: 12:32 PM
 */

namespace common\models;

use common\helpers\FileHelper;
use Yii;
use yii\helpers\Url;

/**
 * Class Media
 * @package common\models
 *
 */
class Media extends BaseMedia
{
    const TYPE = 1;

    const LOCALE_BASE_PATH = '@frontend/web/photo';
    const LOCALE_BASE_URL = '@public/photo';

    const MAX_WIDTH = 750;
    const MAX_HEIGHT = 700;

    //featured image
    const FI_WIDTH = 263;
    const FI_HEIGHT = 125;

    //notification image
    const NI_WIDTH = 60;
    const NI_HEIGHT = 35;

    const TYPE_MP4 = 'mp4';
    const TYPE_WEBM = 'webm';
    const TYPE_PI = 'poster';
    const TYPE_GIF = 'gif';
    const TYPE_I = 'image';
    const TYPE_LI = 'long_image';
    const TYPE_CI = 'crop_image';
    const TYPE_FI = 'featured';
    const TYPE_NI = 'notification';

    /**
     * @param null $type
     * @return bool|string
     */
    public function getPath($type = null)
    {
        list($suffix, $extension) = static::getInfo($type);
        if (null === $extension) {
            $extension = $this->extension;
        }
        return Yii::getAlias(self::LOCALE_BASE_PATH . DIRECTORY_SEPARATOR . $this->getBaseName($suffix, $extension));
    }

    public function getUrl($scheme = true, $type = null)
    {
        if ($this->is_uploaded_to_cdn) {
            $url =  Yii::$app->cloudStorage->getPublicUrl($this->getPath($type));
            return $url;
        }
        list($suffix, $extension) = static::getInfo($type);
        if (null === $extension) {
            $extension = $this->extension;
        }
        return Url::to(self::LOCALE_BASE_URL . '/' . $this->getBaseName($suffix, $extension), $scheme);
    }

    public function getType()
    {
        if ($this->getIsGif()) {
            return self::TYPE_GIF;
        } elseif ($this->getIsLong()) {
            return self::TYPE_LI;
        }
        return self::TYPE_I;
    }


    public function handle($tempFileName)
    {
        parent::handle($tempFileName);
        if ($this->getIsGif()) {
            FileHelper::move(FileHelper::tmpPath($tempFileName), $this->getPath(self::TYPE_GIF));

            //convert to jpeg image
            $success = @FileHelper::convertGifToJpeg($this->getPath(self::TYPE_GIF), $this->getPath(self::TYPE_PI));
            if (!$success) {
                Yii::error('converting ' . $this->getPath(self::TYPE_GIF) . ' to jpeg failed');
            }
            //convert to webm video
            $code = @FileHelper::convertGifToWebm($this->getPath(self::TYPE_GIF), $this->getPath(self::TYPE_WEBM));
            if ($code !== 0) {
                Yii::error('converting ' . $this->getPath(self::TYPE_GIF) . ' to webm failed');
            }
            //convert to mp4 video
            $code = @FileHelper::convertWebmToMp4($this->getPath(self::TYPE_WEBM), $this->getPath(self::TYPE_MP4));
            if ($code !== 0) {
                Yii::error('converting ' . $this->getPath(self::TYPE_WEBM) . ' to mp4 failed');
            }
            //thumbnail
            FileHelper::thumbnail($this->getPath(self::TYPE_PI), self::MAX_WIDTH);

            $type = self::TYPE_PI;
        } else {
            $type = null;

            FileHelper::move(FileHelper::tmpPath($tempFileName), $this->getPath());

            //thumbnail
            FileHelper::thumbnail($this->getPath(), self::MAX_WIDTH);

            //long image
            $height = FileHelper::height($this->getPath());
            $this->setIsLong($height > self::MAX_HEIGHT);

            if ($this->getIsLong()) {
                FileHelper::move($this->getPath(), $this->getPath(self::TYPE_LI));
                FileHelper::crop($this->getPath(self::TYPE_LI), null, self::MAX_HEIGHT, $this->getPath(self::TYPE_CI));
                $type = self::TYPE_LI;
            }
        }

        //featured photo
        FileHelper::thumbnail($this->getPath($type), self::FI_WIDTH, null, $this->getPath(self::TYPE_FI));
        FileHelper::crop($this->getPath(self::TYPE_FI), self::FI_WIDTH, self::FI_HEIGHT, null, 'centerY');

        //notifications
        FileHelper::thumbnail($this->getPath($type), self::NI_WIDTH, null, $this->getPath(self::TYPE_NI));
        FileHelper::crop($this->getPath(self::TYPE_NI), self::NI_WIDTH, self::NI_HEIGHT, null, 'centerY');

        //watermark
        FileHelper::watermark($this->getPath($type));

        if (!$this->update()) {
            $this->throwValidationException();
        }
    }

    public function getFiles()
    {
        $files = [];
        $files[] = $this->getPath(self::TYPE_FI);
        $files[] = $this->getPath(self::TYPE_NI);
        if ($this->getIsLong()) {
            $files[] = $this->getPath(self::TYPE_LI);
            $files[] = $this->getPath(self::TYPE_CI);
        } elseif ($this->getIsGif()) {
            $files[] = $this->getPath(self::TYPE_GIF);
            $files[] = $this->getPath(self::TYPE_WEBM);
            $files[] = $this->getPath(self::TYPE_MP4);
            $files[] = $this->getPath(self::TYPE_PI);
        } else {
            $files[] = $this->getPath();
        }
        return $files;
    }


    public static function getInfo($type)
    {
        switch ($type) {
            case self::TYPE_WEBM:
                return ['_vwebm', 'webm'];
            case self::TYPE_MP4:
                return ['_vmp4', 'mp4'];
            case self::TYPE_PI:
                return ['_pi', 'jpg'];
            case self::TYPE_GIF:
                return ['_ai', null];
            case self::TYPE_CI:
                return ['_ci', null];
            case self::TYPE_LI:
                return ['_li', null];
            case self::TYPE_FI:
                return ['_fi', null];
            case self::TYPE_NI:
                return ['_ni', null];
            default:
                return ['_i', null];
        }
    }

    public function beforeSave($insert)
    {
        $this->target = self::TYPE;
        return parent::beforeSave($insert);
    }
}
