<?php
namespace common\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
abstract class ResetPasswordForm extends Model
{
    public $password;

    protected $token;

    /**
     * @var \common\models\User
     */
    protected $_user = false;


    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Token de redefinição de senha é necessária.');
        }
        $this->token = $token;
        $user = $this->getUser();
        if (!$user) {
            throw new InvalidParamException('Token de redefinição de senha é inválido ou expirou.');
        }

        if ($user->isBanned()) {
            throw new InvalidParamException('Você não pode redefinir a senha para esta conta. Porque ele foi banido.');
        }

        if ($user->isDeleted()) {
            throw new InvalidParamException('Você não pode redefinir a senha para esta conta. Porque ele foi excluído.');
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->getUser();
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }

    /**
     * Finds user by [[token]]
     *
     * @return User|null
     */
    abstract public function getUser();
}
