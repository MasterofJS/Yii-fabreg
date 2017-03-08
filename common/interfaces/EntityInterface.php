<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/3/16
 * Time: 11:49 AM
 */

namespace common\interfaces;


/**
 * Interface EntityInterface
 * @package common\interfaces
 */
interface EntityInterface
{
    public function getId();
    public function getType();
    public function getAuthorId();
    public function getUrl($scheme = true);
}