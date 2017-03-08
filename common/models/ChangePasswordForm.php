<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 3:37 PM
 */

namespace common\models;


use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $old_password;
    public $new_password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password'], 'required'],
            ['new_password', 'string', 'min' => 6],
            ['new_password', 'match', 'pattern' => '/(?=^.{6,20}$)(?=.*\d)(?=.*[A-Za-z]).*$/', 'message' => 'A senha deve conter um mínimo de 6 símbolos e máximo de 20. Contendo ao menos uma letra e um número.'],
            ['old_password','validateOldPassword'],
        ];
    }


    /**
     * Validates the old password.
     * This method serves as the inline validation for old password.
     *
     */
    public function validateOldPassword(){

        if(!\Yii::$app->security->validatePassword($this->old_password, $this->getUser()->getAttribute('password_hash'))){
            $this->addError('old_password', 'Nova Senha incorreta');
        }
    }

    public function attributeLabels()
    {
        return [
            'old_password' => 'Senha Antiga',
            'new_password' => 'Nova Senha',
        ];
    }


    /**
     * change password.
     * @return bool if password was changed.
     * @throws \Exception
     */
    public function changePassword()
    {
        $user = $this->getUser();
        $user->setPassword($this->new_password);
        try {
            if (!$user->save()) {
                $user->throwValidationException();
            }
            return true;
        } catch (\Exception $e) {
            if(!YII_DEBUG){
                \Yii::error($e->getMessage(), __METHOD__);
                return false;
            }else{
                throw $e;
            }
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }
}