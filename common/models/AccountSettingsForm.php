<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 5:25 PM
 */

namespace common\models;


use yii\helpers\ArrayHelper;

class AccountSettingsForm extends UserForm
{
    public $show_nswf;
    public $hide_upvotes;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['email', 'username', 'show_nswf', 'hide_upvotes'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge( parent::rules(),[
            [['username','email'], 'required'],

            ['username', 'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Esse nome de usuário já está em uso.',
                'filter' => ['<>', 'id', $this->getUser()->getId()]
            ],

            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Esse email já está sendo usado por outro usuário.',
                'filter' => ['<>', 'id', $this->getUser()->getId()]
            ],
            [['show_nswf', 'hide_upvotes'], 'boolean'],

        ] );
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

    /**
     * @return User
     * @throws \Exception
     */
    public function save()
    {
        $user = $this->getUser();
        $user->setAttributes($this->getAttributes($this->safeAttributes()));
        $oldEmail = $user->getOldAttribute('email');
        try {
            if (!$user->save()) {
                $user->throwValidationException();
            }
            if($oldEmail != $user->getAttribute('email')){
                $user->sendEmailConfirmationLink();
            }
            return $user;
        } catch (\Exception $e) {
            if(!YII_DEBUG){
                \Yii::error($e->getMessage(), __METHOD__);
                return null;
            }else{
                throw $e;
            }
        }
    }

}