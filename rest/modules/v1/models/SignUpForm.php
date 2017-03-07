<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 3:34 PM
 */

namespace rest\modules\v1\models;


class SignUpForm extends \common\models\SignUpForm
{
    public function getUser()
    {
        return new User();
    }
}
