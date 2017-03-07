<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/24/15
 * Time: 11:30 AM
 */

namespace backend\traits;

/**
 * Class ApprovableTrait
 * @package backend\traits
 *
 */

trait ApprovableStatusTrait
{
    use StatusTrait;

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => ['text' => 'Pending', 'class' => 'warning'],
            self::STATUS_APPROVED => ['text' => 'Post banned', 'class' => 'danger'],
            self::STATUS_DISAPPROVED => ['text' => 'Report rejected', 'class' => 'success'],
        ];
    }
}