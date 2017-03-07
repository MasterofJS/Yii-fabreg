<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 5:28 PM
 */

namespace rest\modules\v1\models;


class ProfileForm extends \common\models\ProfileForm
{
    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

}