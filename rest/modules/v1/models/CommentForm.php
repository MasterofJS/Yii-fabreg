<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 10:28 AM
 */

namespace rest\modules\v1\models;


class CommentForm extends \common\models\CommentForm
{
    public function getComment()
    {
        return new Comment();
    }

}