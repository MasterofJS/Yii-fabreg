<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/21/16
 * Time: 4:48 PM
 */

namespace rest\modules\v1\models;


class ResetPasswordForm extends \common\models\ResetPasswordForm
{
    /**
     * Finds user by [[token]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByPasswordResetToken($this->token);
        }
        return $this->_user;
    }
}