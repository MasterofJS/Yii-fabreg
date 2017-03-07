<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 3:37 PM
 */

namespace backend\models;


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
            ['old_password', 'validateOldPassword'],
        ];
    }


    /**
     * Validates the old password.
     * This method serves as the inline validation for old password.
     *
     */
    public function validateOldPassword()
    {

        if (!\Yii::$app->security->validatePassword($this->old_password, $this->getAdmin()->getAttribute('password_hash'))) {
            $this->addError('old_password', 'Nova Senha incorreta');
        }
    }

    public function attributeLabels()
    {
        return [
            'old_password' => 'Old Password',
            'new_password' => 'New Password',
        ];
    }


    /**
     * change password.
     * @return bool if password was changed.
     * @throws \Exception
     */
    public function changePassword()
    {
        $admin = $this->getAdmin();
        $admin->setPassword($this->new_password);
        try {
            if (!$admin->save()) {
                $admin->throwValidationException();
            }
            return true;
        } catch (\Exception $e) {
            if (!YII_DEBUG) {
                \Yii::error($e->getMessage(), __METHOD__);
                return false;
            } else {
                throw $e;
            }
        }
    }

    /**
     * @return Admin
     */
    public function getAdmin()
    {
        return \Yii::$app->user->identity;
    }
}