<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 4:59 PM
 */

namespace rest\modules\v1\models;


class PostForm extends \common\models\PostForm
{
    public function getPost()
    {
        return new Post();
    }
}