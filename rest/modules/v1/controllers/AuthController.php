<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/23/16
 * Time: 5:57 PM
 */

namespace rest\modules\v1\controllers;


use rest\modules\v1\models\LoginForm;
use rest\modules\v1\models\SignUpForm;
use rest\modules\v1\models\User;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class AuthController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'sign-up'],
                'rules' => [
                    [
                        'actions' => ['sign-up'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionSignUp()
    {
        $model = new SignUpForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if (!$model->validate()) {
            return $model;
        }
        /** @var $user User */
        $user = $model->signUp();
        if ($user) {
            $user->sendWelcome();
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', $user->getViewUrl());
            \Yii::$app->user->login($user, \Yii::$app->params['user.rememberMeDuration']);
        } else {
            throw new ServerErrorHttpException('Falha de registro de usuário por motivo desconhecido.');
        }
        return $user->full();
    }


    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->user->identity->full();
        }
        $model = new LoginForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        /** @var $user User */
        $user = $model->signIn();
        if ($user) {
            $response = \Yii::$app->getResponse();
            $response->getHeaders()->set('Location', $user->getViewUrl());
        } else {
            throw new ServerErrorHttpException("Falha de entrar de usuário por motivo desconhecido.");
        }
        return $user->full();
    }


    public function actionLogout()
    {
        \Yii::$app->user->logout();
        \Yii::$app->getResponse()->setStatusCode(204);
    }
}