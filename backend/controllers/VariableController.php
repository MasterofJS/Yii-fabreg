<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/23/16
 * Time: 6:23 PM
 */

namespace backend\controllers;

use backend\models\Variable;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class VariableController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index' => ['get'],
                    'delete' => ['post'],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => 'backend\models\Variable'
            ],
        ];
    }


    /**
     * Creates a new Variable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = 'form';
        $model = new Variable(['scenario' => 'create']);
        $validation = $this->performAjaxValidation($model);
        if ($validation !== null) {
            return $validation;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Variable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = 'form';
        $model = $this->findModel($id);
        $validation = $this->performAjaxValidation($model);
        if ($validation !== null) {
            return $validation;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Variable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * import missing variables
     */
    public function actionImport()
    {
        $variables = require(\Yii::getAlias('@common/data/variables.php'));
        return $this->import($variables);
    }

    public function actionImportNew()
    {
        $variables = Variable::getNewVariables(require(\Yii::getAlias('@common/data/variables.php')));
        return $this->import($variables);
    }

    /**
     * import missing variables
     */
    public function actionReset()
    {
        $variables = require(\Yii::getAlias('@common/data/variables.php'));
        $counter = 0;
        if (!empty($variables)) {
            foreach ($variables as $namespace => $keys) {
                foreach ($keys as $key => $var) {
                    /** @var $model Variable */
                    $model = Variable::find()->andWhere(['namespace' => $namespace, 'key' => $key])->one();
                    if (!$model) {
                        continue;
                    }
                    $model->value = $var['value'];
                    $model->type = $var['type'];
                    if ($model->save()) {
                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }

    protected function import($variables)
    {
        $counter = 0;
        if (!empty($variables)) {
            foreach ($variables as $namespace => $keys) {
                foreach ($keys as $key => $var) {
                    $model = new Variable(['scenario' => 'create']);
                    $model->key = $key;
                    $model->value = $var['value'];
                    $model->namespace = $namespace;
                    $model->type = $var['type'];
                    if ($model->save()) {
                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }

    /**
     * Finds the Variable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Variable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Variable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}