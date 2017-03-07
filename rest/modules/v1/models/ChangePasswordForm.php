<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 3:52 PM
 */

namespace rest\modules\v1\models;


class ChangePasswordForm extends \common\models\ChangePasswordForm
{
    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }
}