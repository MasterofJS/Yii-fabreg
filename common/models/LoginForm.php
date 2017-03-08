<?php
namespace common\models;

use Yii;
use yii\base\Model;


abstract class LoginForm extends Model
{
    public $email;
    public $password;
    public $remember_me = true;

    protected $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],

            [['email'], 'email'],
            // rememberMe must be a boolean value
            ['remember_me', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Senha',
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
                $this->addError('password', 'Email ou senha incorreta.');
                $this->addError('email', 'Email ou senha incorreta.');
            }elseif($user->isBanned()){
                $this->addError('email', 'A conta com esse email foi banida.');
            }elseif($user->isDeleted()){
                $this->addError('email', 'Essa conta foi deletada. Entre em contato conosco para reativação.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return null|User
     */
    public function signIn()
    {
        $type = null;
        $duration = \Yii::$app->get('variables')->get('login', 'remember', $type);
        $duration = Variable::toSeconds($duration, $type);
        if (Yii::$app->user->login($this->getUser(), $this->remember_me ? $duration  : 0)) {
            return $this->getUser();
        }
        return null;

    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    abstract public function getUser();
}