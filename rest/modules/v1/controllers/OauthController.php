<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/1/16
 * Time: 2:15 PM
 */

namespace rest\modules\v1\controllers;

use rest\modules\v1\components\Facebook;
use rest\modules\v1\components\GoogleOAuth;
use rest\modules\v1\models\Auth;
use Yii;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class OauthController extends Controller
{
    use OauthTrait;
    /**
     * @var ClientInterface
     */
    private $client;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['disconnect'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionFacebook()
    {
        $this->client = new Facebook();
        $this->client->setAccessToken([]);
        $token = $this->client->getAccessToken();
        $token->setToken(Yii::$app->request->getBodyParam('access_token'));
        $token->setExpireDuration(Yii::$app->request->getBodyParam('expires_in'));
        $this->auth();
        return Yii::$app->getUser()->getIdentity()->full();
    }

    public function actionGoogle()
    {
        $this->client = new GoogleOAuth();
        $this->client->setAccessToken([]);
        $token = $this->client->getAccessToken();
        $token->setToken(Yii::$app->request->getBodyParam('access_token'));
        $token->setExpireDuration(Yii::$app->request->getBodyParam('expires_in'));
        $this->auth();
        return Yii::$app->getUser()->getIdentity()->full();
    }

    public function actionDisconnect($provider)
    {
        $auth = Auth::findOne([
            'user_id' => Yii::$app->user->id,
            'source' => $provider
        ]);

        if ($auth && !$auth->delete()) {
            throw new ServerErrorHttpException(
                "Oops! something went wrong. We were unable to disconnect your account from your {$provider} account!"
            );
        }
        Yii::$app->response->setStatusCode(204);
    }
}
