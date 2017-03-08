<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
abstract class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @var \common\models\User
     */
    protected $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Não há nenhum usuário registrado com esse email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user = $this->getUser();

        if (!$user || $user->isDeleted() || $user->isBanned()) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }
        
        if (!$user->save()) {
            return false;
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'reset-password'],
                ['user' => $user]
            )
            ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Sua senha Unicorno foi resetada com sucesso')
            ->send();
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    abstract public function getUser();
}
