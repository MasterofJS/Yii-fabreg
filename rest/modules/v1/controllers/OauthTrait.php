<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 6/21/16
 * Time: 10:52 AM
 */

namespace rest\modules\v1\controllers;

use common\models\Variable;
use rest\components\DataValidationFailedHttpException;
use rest\modules\v1\models\Auth;
use rest\modules\v1\models\SignUpForm;
use rest\modules\v1\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

trait OauthTrait
{
    /**
     * @var ClientInterface
     */
    private $client;
    
    public function auth()
    {
        $attributes = $this->client->getUserAttributes();
        $response = \Yii::$app->getResponse();
        /** @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /** @var User $user */
                $user = $auth->user;

                if ($user->isBanned()) {
                    throw new ForbiddenHttpException('A conta com esse email foi banida.');
                } elseif ($user->isDeleted()) {
                    throw new ForbiddenHttpException(
                        'Essa conta foi deletada. Entre em contato conosco para reativação.'
                    );
                }

                $response->getHeaders()->set('Location', $user->getViewUrl());
                Yii::$app->user->login($user);
            } else { // signup
                try {
                    $attributes = Auth::parseAttributes($this->client);
                } catch (\Exception $ex) {
                    if (!YII_DEBUG) {
                        \Yii::error($ex->getMessage(), __METHOD__);
                        throw new ServerErrorHttpException(
                            'Ops! algo deu errado. Não fomos capazes de obter informações sobre você!'
                        );
                    } else {
                        throw $ex;
                    }
                }

                /** @var User $user */
                $user = User::find()->where(['email' => $attributes['email']])->one();
                if (!empty($attributes['email']) && $user) {
                    if ($user->isBanned()) {
                        throw new ForbiddenHttpException('Esta conta foi banida.');
                    } elseif ($user->isDeleted()) {
                        $user->status = User::STATUS_ACTIVE;
                        $user->save();
                    }
                    $auth = new Auth([
                        'user_id' => $user->id,
                        'source' => $this->client->getId(),
                        'source_id' => (string)$attributes['id'],
                    ]);
                    try {
                        if (!$auth->save()) {
                            $auth->throwValidationException();
                        }

                        $response->setStatusCode(201);
                        Yii::$app->user->login($user);
                    } catch (\Exception $ex) {
                        if (!YII_DEBUG) {
                            \Yii::error($ex->getMessage(), __METHOD__);
                            throw new ServerErrorHttpException(
                                sprintf('Failed to link %s account for unknown reason.', $this->client->getTitle())
                            );
                        } else {
                            throw $ex;
                        }
                    }
                } else {
                    $model = new SignUpForm();
                    if (isset($attributes['avatar'])) {
                        $attributes['avatar'] = Auth::upload($attributes['avatar']);
                    }
                    $model->load($attributes, '');
                    if (!$model->validate()) {
                        throw new DataValidationFailedHttpException(
                            'Precisamos de mais informações para que consiga entrar no site'
                        );
                    }
                    /** @var $user User */
                    $user = $model->signUp();
                    if ($user) {
                        $response = \Yii::$app->getResponse();
                        $response->setStatusCode(201);
                        $response->getHeaders()->set('Location', $user->getViewUrl());
                        $type = null;
                        $duration = \Yii::$app->get('variables')->get('login', 'remember', $type);
                        $duration = Variable::toSeconds($duration, $type);
                        Yii::$app->user->login($user, $duration);
                        Yii::$app
                            ->mailer
                            ->compose(
                                ['html' => 'password'],
                                ['user' => $user, 'password' => $model->password]
                            )
                            ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
                            ->setTo($user->email)
                            ->setSubject('Unicornada crescendo!')
                            ->send();
                    } else {
                        throw new ServerErrorHttpException('Falha de registro de usuário por motivo desconhecido.');
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$attributes['id'],
                ]);
                try {
                    if (!$auth->save()) {
                        $auth->throwValidationException();
                    }
                } catch (\Exception $ex) {
                    if (!YII_DEBUG) {
                        \Yii::error($ex->getMessage(), __METHOD__);
                        throw new ServerErrorHttpException('Falha de conexão com esta conta por motivo desconhecido');
                    } else {
                        throw $ex;
                    }
                }
            } else { // there's existing auth
                if ($auth->user_id == Yii::$app->user->id) {
                    throw new ForbiddenHttpException('Essa conta ja esta conectada com sua conta Unicorno');
                } else {
                    throw new ForbiddenHttpException('Esta conta ja esta sendo utilizada');
                }
            }
        }
    }
}
