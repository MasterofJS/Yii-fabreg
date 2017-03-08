<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 5/10/16
 * Time: 11:32 AM
 */

namespace common\interfaces;

interface CloudStorageInterface
{
    /**
     * Saves a file to cloud storage
     * @param string $file the file uploaded.
     * The [[UploadedFile::$tempName]] will be used as the source file.
     */
    public function upload($file);

    /**
     * Downloads file(s) to the local filesystem
     *
     * @param string $prefix Only download objects that use this key prefix
     * @param string $dir Directory to download to
     */
    public function download($prefix = '', $dir = null);

    /**
     * Saves temp file to cloud storage
     * @param $tmpFileName
     * @param string $prefix
     * @return string the url of temp file
     */
    public function uploadTmp($tmpFileName, $prefix = '');
    /**
     * Saves temp file in locale filesystem
     * @param $tmpFileName
     * @param string $prefix
     */
    public function downloadTmp($tmpFileName, $prefix = '');
    /**
     * Removes a file
     * @param string|array $file the path of the file(s) to remove
     * @return int number of deleted files
     */
    public function delete($file);

    /**
     * Returns the public url of the file or empty string if the file does not exists.
     * @param string $file the path of the file to access
     * @return string
     */
    public function getPublicUrl($file);

    /**
     * @return mixed
     */
    public function getClient();

    public function deleteAll();
}
