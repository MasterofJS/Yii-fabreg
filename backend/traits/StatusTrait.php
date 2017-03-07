<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/24/15
 * Time: 1:19 PM
 */

namespace backend\traits;


use yii\base\NotSupportedException;

trait StatusTrait
{
    /**
     * @param mixed $status
     * @param mixed $attr
     * @return mixed
     * @throws NotSupportedException
     */
    public static function getStatus($status, $attr)
    {
        $array = self::getStatuses();
        return isset($array[$status]) ? $array[$status][$attr] : $status;
    }

    /**
     *
     * @throws NotSupportedException
     * @return array
     */
    public static function getStatuses()
    {
        throw new NotSupportedException(__TRAIT__ . ' has not implementation for method ' . __METHOD__);
    }
}