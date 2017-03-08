<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 3/13/16
 * Time: 2:49 PM
 */

namespace common\components;


use common\helpers\FileHelper;

class UploadedFile extends \yii\web\UploadedFile
{
    public $isUploadedFromUrl = false;

    /**
     * Returns an uploaded file according to the given url.
     * @param string $url the url.
     * @return UploadedFile the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified name.
     */
    public static function getInstanceByUrl($url)
    {
        if(FileHelper::copyFromUrl($url, $tempName)){
            return new static([
                'name' => basename($tempName),
                'tempName' => $tempName,
                'type' => FileHelper::getMimeType($tempName, null, false),
                'size' => filesize($tempName),
                'error' => UPLOAD_ERR_OK,
                'isUploadedFromUrl' => true
            ]);
        }
        return null;
    }

    /**
     * Saves the uploaded file.
     * Note that this method uses php's move_uploaded_file() method. If the target file `$file`
     * already exists, it will be overwritten.
     * @param string $file the file path used to save the uploaded file
     * @param boolean $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return boolean true whether the file is saved successfully
     * @see error
     */
    public function saveAs($file, $deleteTempFile = true)
    {
        if ($this->error == UPLOAD_ERR_OK) {
            if(is_uploaded_file($this->tempName)){
                if ($deleteTempFile) {
                    return move_uploaded_file($this->tempName, $file);
                } else {
                    return copy($this->tempName, $file);
                }
            }elseif($this->isUploadedFromUrl){
                if ($deleteTempFile) {
                    return FileHelper::move($this->tempName, $file);
                } else {
                    return FileHelper::copyFromPath($this->tempName, $file);
                }
            }
        }
        return false;
    }

    public function getExtension()
    {
        $mimeType = FileHelper::getMimeType($this->tempName, null, false);
        switch($mimeType){
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            default:
                return parent::getExtension();
        }
    }
}