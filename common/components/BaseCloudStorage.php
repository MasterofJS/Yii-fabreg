<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 5/10/16
 * Time: 12:14 PM
 */

namespace common\components;

use common\helpers\FileHelper;
use common\interfaces\CloudStorageInterface;
use yii\base\Component;

abstract class BaseCloudStorage extends Component implements CloudStorageInterface
{
    public $localeFileSystemBasePath = '@frontend/web/';
    
    /**
     * @inheritdoc
     */
    public function downloadTmp($tmpFileName, $prefix = '')
    {
        $file = FileHelper::tmpPath($tmpFileName, $prefix);
        $name = $this->getName($file);
        $this->download($name);
        $this->delete($name);
    }

   /**
    * @inheritdoc
    */
    public function uploadTmp($tmpFileName, $prefix = '')
    {
        $file = FileHelper::tmpPath($tmpFileName, $prefix);
        $this->upload($file);
        unlink($file);
        $name = $this->getName($file);
        return $this->getPublicUrl($name);
    }

    public function getName($file)
    {
        return str_replace(
            DIRECTORY_SEPARATOR,
            '/',
            str_replace(\Yii::getAlias($this->localeFileSystemBasePath), '', \Yii::getAlias($file))
        );
    }
}
