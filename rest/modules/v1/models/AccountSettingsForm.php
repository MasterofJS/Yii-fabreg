<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 5:29 PM
 */

namespace rest\modules\v1\models;


class AccountSettingsForm extends \common\models\AccountSettingsForm
{
    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

}