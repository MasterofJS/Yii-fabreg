<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/21/16
 * Time: 4:30 PM
 */

namespace rest\modules\v1\controllers;


use rest\modules\v1\models\PasswordResetRequestForm;
use rest\modules\v1\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class RecoveryController extends Controller
{
    public function actionReset($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }

        if (!$model->resetPassword()) {
            throw new ServerErrorHttpException('Desculpe, não conseguimos resetar sua senha.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionRequest()
    {
        $model = new PasswordResetRequestForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        if (!$model->sendEmail()) {
            throw new ServerErrorHttpException('Desculpe, não conseguimos resetar a senha para o email escolhido.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}