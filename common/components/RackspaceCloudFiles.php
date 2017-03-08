<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 5/10/16
 * Time: 11:06 AM
 */

namespace common\components;

use yii\base\InvalidConfigException;
use OpenCloud\Rackspace;

/**
 * Class RackspaceCloudFiles
 * @package common\components
 * @property-read Rackspace $client
 */
class RackspaceCloudFiles extends BaseCloudStorage
{
    public $username;
    public $apiKey;
    public $containerName;
    public $publicLink;
    /**
     * @var string The region (DFW, IAD, ORD, LON, SYD)
     */
    public $region;
    public $identityEndpoint = Rackspace::US_IDENTITY_ENDPOINT;

    /**
     * @var Rackspace
     */
    private $_client;

    private $_container;

    /**
     * @inheritdoc
     */
    public function init()
    {
        foreach (['username', 'apiKey', 'containerName', 'region'] as $attribute) {
            if ($this->$attribute === null) {
                throw new InvalidConfigException(strtr('"{class}::{attribute}" cannot be empty.', [
                    '{class}' => static::className(),
                    '{attribute}' => '$' . $attribute
                ]));
            }
        }
        parent::init();
    }

    public function getContainer()
    {
        if (null === $this->_container) {
            $this->_container = $this->getClient()
                ->objectStoreService(null, $this->region)
                ->getContainer($this->containerName);
            if (!$this->_container) {
                $this->_container = $this->getClient()
                    ->objectStoreService(null, $this->region)
                    ->createContainer($this->containerName);
                $this->_container->enableCdn();
            }
        }
        return $this->_container;
    }

    /**
     * Returns a Rackspace instance
     * @return Rackspace
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new Rackspace($this->identityEndpoint, array(
                'username' => $this->username,
                'apiKey'   => $this->apiKey,
            ));
        }
        return $this->_client;
    }

    /**
     * Saves a file to cloud storage
     * @param string $file the file uploaded.
     * The [[UploadedFile::$tempName]] will be used as the source file.
     */
    public function upload($file)
    {
        $name = $this->getName($file);
        $handle = fopen($file, 'r');
        $this->getContainer()->uploadObject($name, $handle);
    }

    /**
     * Downloads file(s) to the local filesystem
     *
     * @param string $prefix Only download objects that use this key prefix
     * @param string $dir Directory to download to
     */
    public function download($prefix = '', $dir = null)
    {
        $object = @$this->getContainer()->getObject($prefix);
        if ($object) {
            $stream = $object->getContent();
            $stream->rewind();
            $dest = rtrim(\Yii::getAlias($this->localeFileSystemBasePath), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . $prefix;
            file_put_contents($dest, $stream->getStream());
        }
    }

    /**
     * Removes a file
     * @param string|array $file the path of the file(s) to remove
     * @return int number of deleted files
     */
    public function delete($file)
    {
        if (!is_array($file)) {
            $file = [$file];
        }
        foreach ($file as $item) {
            $object = @$this->getContainer()->getObject($this->getName($item));
            if ($object) {
                $object->delete();
            }
        }
        return count($file);
    }

    public function deleteAll()
    {
        $this->getContainer()->deleteAllObjects();
    }

    /**
     * Returns the public url of the file or empty string if the file does not exists.
     * @param string $file the path of the file to access
     * @return string
     */
    public function getPublicUrl($file)
    {
        $name = $this->getName($file);
        if (null !== $this->publicLink) {
            return rtrim($this->publicLink, '/') . '/' . ltrim($name, '/');
        }
        $object = @$this->getContainer()->getObject($name);
        if ($object) {
            return strval($object->getPublicUrl());
        }
        return '';
    }
}
