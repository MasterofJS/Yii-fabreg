<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 3:53 PM
 */

namespace common\helpers;

use yii\base\InvalidParamException;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;

class FileHelper extends BaseFileHelper
{
    const  FOLDER_MODE = 02775;
    const  FILE_MODE = 0664;
    const  TMP_BASE_PATH = '@frontend/web/tmp';
    const  TMP_BASE_URL = '@public/tmp';

    public static function createDirectory($path, $mode = self::FOLDER_MODE, $recursive = true)
    {
        return parent::createDirectory($path, self::FOLDER_MODE, $recursive);
    }

    public static function chmod($path)
    {
        if (file_exists($path)) {
            chmod($path, is_dir($path) ? self::FOLDER_MODE : self::FILE_MODE);
        }
    }

    public static function unlink($file)
    {
        if (is_file($file)) {
            unlink($file);
        }
    }

    public static function crop($src, $width, $height, $dest = null, $start = [0, 0])
    {
        if (!is_file($src)) {
            return;
        }

        ini_set('memory_limit', '256M');
        if (null == $dest) {
            $dest = $src;
        }

        if ($dest != $src && !is_file($dest)) {
            $chmod = true;
        }

        if (!is_dir(dirname($dest))) {
            static::createDirectory(dirname($dest));
        }

        if (is_string($start)) {
            if (!strcmp('centerY', $start)) {
                $start = [0, static::centerY($src, $height)];
            } else {
                $start = [0, 0];
            }
        }
        ImageHelper::crop($src, $width, $height, $start)->save($dest);

        if (isset($chmod)) {
            self::chmod($dest);
        }
    }

    public static function thumbnail($src, $width, $height = null, $dest = null, $options = ['quality' => 100])
    {
        if (!is_file($src)) {
            return;
        }

        ini_set('memory_limit', '256M');
        if (null == $dest) {
            $dest = $src;
        }
        if ($dest != $src && !is_file($dest)) {
            $chmod = true;
        }

        if (!is_dir(dirname($dest))) {
            static::createDirectory(dirname($dest));
        }
        ImageHelper::thumbnail($src, $width, $height)->save($dest, $options);
        if (isset($chmod)) {
            self::chmod($dest);
        }
    }

    public static function width($src)
    {
        if (!is_file($src)) {
            return null;
        }
        clearstatcache();
        $img = ImageHelper::getImagine()->open($src);
        return $img->getSize()->getWidth();
    }

    public static function height($src)
    {
        if (!is_file($src)) {
            return null;
        }
        clearstatcache();
        $img = ImageHelper::getImagine()->open($src);
        return $img->getSize()->getHeight();
    }

    public static function size($src)
    {
        if (!is_file($src)) {
            return null;
        }
        clearstatcache();
        $img = ImageHelper::getImagine()->open($src);
        return [$img->getSize()->getWidth(), $img->getSize()->getHeight()];
    }

    public static function centerY($src, $height)
    {
        if (!is_file($src)) {
            return null;
        }
        $imgHeight = static::height($src);
        $y = max(0, round($imgHeight / 2) - round($height / 2));
        return $y;
    }

    public static function watermark($src, $dest = null)
    {
        if (!is_file($src)) {
            return;
        }

        ini_set('memory_limit', '256M');
        if (null === $dest) {
            $dest = $src;
        }
        list($width, $height) = static::size($src);

        $positions = ['left' => 'v', 'top' => 'h', 'right' => 'v', 'bottom' => 'h'];
        $watermarks = [];

        $watermark = \Yii::getAlias('@frontend/web/logo_horizontal.png');
        list($w, $h) = static::size($watermark);
        $watermarks['h'] = [$watermark, $w, $h];

        $watermark = \Yii::getAlias('@frontend/web/logo_vertical.png');
        list($w, $h) = static::size($watermark);
        $watermarks['v'] = [$watermark, $w, $h];

        $position = array_rand($positions);
        $watermark = $watermarks[$positions[$position]][0];
        $w = $watermarks[$positions[$position]][1];
        $h = $watermarks[$positions[$position]][2];
        switch ($position) {
            case 'bottom':
                $nx = rand(3, $width - $w);
                $ny = $height - $h;
                break;
            case 'right':
                $nx = $width - $w;
                $ny = rand(3, $height - $h);
                break;
            case 'top':
                $nx = rand(3, $width - $w);
                $ny = 0;
                break;
            case 'left':
                $nx = 0;
                $ny = rand(3, $height - $h);
                break;
        }

        if (!isset($nx, $ny)) {
            return;
        }
        ImageHelper::watermark($src, $watermark, [$nx, $ny])->save($dest);
    }

    public static function move($src, $dest)
    {
        if (!is_file($src)) {
            return false;
        }
        if (!is_dir(dirname($dest))) {
            static::createDirectory(dirname($dest));
        }
        if (rename($src, $dest)) {
            return true;
        }
        return false;
    }

    public static function copyFromPath($src, $dest)
    {
        if (!is_file($src)) {
            return false;
        }

        if (!is_dir(dirname($dest))) {
            static::createDirectory(dirname($dest));
        }
        return copy($src, $dest);
    }

    public static function copyFromUrl($url, &$dest = null)
    {
        $dest = static::tmpFile();

        if (!is_dir(dirname($dest))) {
            static::createDirectory(dirname($dest));
        }
        return @copy($url, $dest);
    }

    public static function convertGifToJpeg($gifFileName, $jpegFileName, $quality = 100)
    {
        $image = imagecreatefromgif($gifFileName);
        imagejpeg($image, $jpegFileName, $quality);
        imagedestroy($image);
        return true;
    }

    /**
     * @param string $gifFileName
     * @param string $webmFileName
     * @param int $crf
     * @param int $maxBitrate
     * @return int status of the executed command. O if successful
     */
    public static function convertGifToWebm($gifFileName, $webmFileName, $crf = 4, $maxBitrate = 100)
    {
        system(
            sprintf(
                '%s -i %s -c:v libvpx -crf %d -b:v %dK %s',
                \Yii::$app->params['ffmpeg'],
                $gifFileName,
                $crf,
                $maxBitrate,
                $webmFileName
            ),
            $return_var
        );
        return $return_var;
    }

    /**
     * @param string $webmFileName
     * @param string $mp4FileName
     * @return int status of the executed command. O if successful
     */
    public static function convertWebmToMp4($webmFileName, $mp4FileName)
    {
        system(
            sprintf(
                '%s -i %s -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" %s',
                \Yii::$app->params['ffmpeg'],
                $webmFileName,
                $mp4FileName
            ),
            $return_var
        );
        return $return_var;
    }

    public static function secureName($suffix = '')
    {
        return sha1(microtime()) . $suffix;
    }

    public static function tmpFile($dir = self::TMP_BASE_PATH, $extension = 'tmp')
    {
        return \Yii::getAlias($dir) . DIRECTORY_SEPARATOR . static::secureName('.' . $extension);
    }

    public static function tmpPath($tmpFileName, $prefix = '')
    {
        return \Yii::getAlias(self::TMP_BASE_PATH . DIRECTORY_SEPARATOR . $prefix . $tmpFileName);
    }

    public static function tmpUrl($tmpFileName, $prefix = '')
    {
        return Url::to(self::TMP_BASE_URL . '/' . $prefix . $tmpFileName);
    }

    public static function deleteFiles($dir, $filter = '*')
    {
        $dir = \Yii::getAlias($dir);
        if (!is_dir($dir)) {
            throw new InvalidParamException("The dir argument must be a directory: $dir");
        }
        foreach (glob($dir . DIRECTORY_SEPARATOR . $filter) as $file) {
            unlink($file);
        }
    }

    public static function findFilesV2($dir, $filter = '*')
    {
        $dir = \Yii::getAlias($dir);
        if (!is_dir($dir)) {
            throw new InvalidParamException("The dir argument must be a directory: $dir");
        }
        $list = [];
        foreach (glob($dir . DIRECTORY_SEPARATOR . $filter) as $file) {
            $list[] = $file;
        }
        return $list;
    }
}
