<?php
namespace backend\models;

use Yii;
use yii\base\Model;


class LoginForm extends Model
{
    public $username;
    public $password;
    public $remember_me = false;

    protected $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['remember_me', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect username or password.');
                $this->addError('email', 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return Admin|null
     */
    public function signIn()
    {
        $user = $this->getUser();
        if ($this->validate() && Yii::$app->user->login($this->getUser(), $this->remember_me ? 3600 * 24 : 0)) {
            $user->last_login = Admin::createDateTime();
            $user->save(false);
            return $this->getUser();
        }
        return null;

    }

    /**
     * Finds user by [[username]]
     *
     * @return Admin|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Admin::findByUsername($this->username);
        }
        return $this->_user;
    }
}