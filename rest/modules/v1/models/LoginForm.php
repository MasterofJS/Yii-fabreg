<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 7:15 PM
 */

namespace rest\modules\v1\models;


class LoginForm extends \common\models\LoginForm
{

    /**
     * Finds user by [[Email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->where(['email' => $this->email])->one();
        }
        return $this->_user;
    }
}