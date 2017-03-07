<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/21/16
 * Time: 5:39 PM
 */

namespace rest\modules\v1\controllers;


use rest\modules\v1\models\User;
use Yii;
use yii\web\ServerErrorHttpException;

class AccountController extends Controller
{
    public function actionActivation($token)
    {
        $user = User::findByEmailConfirmationToken($token);
        if ($user) {
            $user->removeEmailConfirmationToken();
            $user->save();
        } elseif ($user === false) {
            throw new ServerErrorHttpException('Desculpe, parece que sua ativação expirou.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}