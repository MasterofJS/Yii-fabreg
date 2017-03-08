<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/1/16
 * Time: 2:15 PM
 */

namespace frontend\controllers;

use rest\modules\v1\controllers\OauthTrait;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class OauthController extends Controller
{
    use OauthTrait;
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\authclient\AuthAction',
                'clientIdGetParamName' => 'provider',
                'successCallback' => [$this, 'onAuthSuccess'],
                'successUrl' => Url::to(['site/index'])
            ],
        ];
    }
    /**
     * @param $client \yii\authclient\ClientInterface
     * @return Response | null;
     */
    public function onAuthSuccess($client)
    {
        $this->client = $client;
        try {
            $this->auth();
        } catch (HttpException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
    }
}
