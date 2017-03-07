<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 12:45 PM
 */

namespace rest\modules\v1\components;


class User extends \yii\web\User
{
    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        throw new LoginRequiredException('Login Required');
    }
}