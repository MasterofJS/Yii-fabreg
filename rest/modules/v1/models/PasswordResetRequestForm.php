<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/21/16
 * Time: 4:48 PM
 */

namespace rest\modules\v1\models;


class PasswordResetRequestForm extends \common\models\PasswordResetRequestForm
{
    /**
     * Finds user by [[Email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->andWhere(['email' => $this->email])->one();
        }
        return $this->_user;
    }
}