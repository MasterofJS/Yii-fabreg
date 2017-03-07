<?php
/**
 * Created by PhpStorm.
 * User: bigdrop
 * Date: 28.03.16
 * Time: 14:32
 */

namespace rest\modules\v1\controllers;

use frontend\models\ContactForm;
use yii\web\ServerErrorHttpException;

class ContactController extends Controller
{

    public function actionSendForm()
    {
        $form = new ContactForm();
        $form->attributes = \Yii::$app->request->post();
        if (!$form->validate()) {
            return $form;
        }
        if (!$form->sendEmail()) {
            throw new ServerErrorHttpException('Houve um erro ao enviar e-mail.');
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }
}
