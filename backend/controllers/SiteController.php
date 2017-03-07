<?php
namespace backend\controllers;

use backend\models\Admin;
use backend\models\ChangePasswordForm;
use backend\models\Post;
use backend\models\Report;
use backend\models\User;
use Yii;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'delete-admin' => ['post'],
                ],
            ],
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['update-user', 'update-post', 'update-report', 'country-statistic', 'age-statistic', 'gender-statistic'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'admins' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => Admin::className()
            ],
            'users' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => User::className()
            ],
            'update-user' => [
                'class' => 'backend\controllers\actions\UpdateAction',
                'modelClass' => User::className()
            ],
            'update-post' => [
                'class' => 'backend\controllers\actions\UpdateAction',
                'modelClass' => Post::className()
            ],
            'update-report' => [
                'class' => 'backend\controllers\actions\UpdateAction',
                'modelClass' => Report::className()
            ],
            'posts' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => Post::className()
            ],
            'reports' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => Report::className()
            ],
        ];
    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'admins' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteAdmin($id)
    {
        if (Yii::$app->user->id != $id) {
            $admin = Admin::findOne($id);
            if ($admin && strcmp('admin', $admin->username)) {
                $admin->delete();
            }
        }
        return $this->redirect(['admins']);
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'admins' page.
     * @return mixed
     */
    public function actionCreateAdmin()
    {
        $this->layout = 'form';
        $model = new Admin();
        $validation = $this->performAjaxValidation($model);
        if ($validation !== null) {
            return $validation;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['admins']);
        } else {
            return $this->render('admin', [
                'model' => $model,
            ]);
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = 'auth';

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->signIn()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('/');
    }

    public function actionMultiReportUpdate($status)
    {
        Report::multiUpdate(json_decode(Yii::$app->request->post('params')), $status) ?
            Yii::$app->response->setStatusCode(204) : Yii::$app->response->setStatusCode(422);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changePassword()) {
                Yii::$app->session->setFlash('success', 'Password has been changed successfully');
            } else {
                Yii::$app->session->setFlash('error', 'Unable to change password');
            }
        }
        return $this->goBack();

    }

    public function actionCountryStatistic()
    {
        return User::byCountry();
    }

    public function actionAgeStatistic()
    {
        return User::byAge();
    }

    public function actionGenderStatistic()
    {
        return User::byGender();
    }
}
