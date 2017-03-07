<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/23/16
 * Time: 6:23 PM
 */

namespace backend\controllers;


use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        parent::init();
        \Yii::$container->set('yii\data\Pagination', ['pageSizeLimit' => [10, 500]]);
    }


    public function render($view, $params = [])
    {
        $params['frontendUrlManager'] = \Yii::$app->get('frontendUrlManager');
        $this->registerBaseAssets();
        return parent::render($view, $params);
    }

    public function renderAjax($view, $params = [])
    {
        $params['frontendUrlManager'] = \Yii::$app->get('frontendUrlManager');
        return parent::renderAjax($view, $params);
    }

    public function renderPartial($view, $params = [])
    {
        $params['frontendUrlManager'] = \Yii::$app->get('frontendUrlManager');
        return parent::renderPartial($view, $params);
    }


    private function registerBaseAssets()
    {
        \backend\assets\BaseAsset::register($this->view);
        \yii\web\YiiAsset::register($this->view);
    }

    /**
     * @param Model $model
     * @return array
     */
    protected function performAjaxValidation($model)
    {
        if (\Yii::$app->request->isAjax
            && \Yii::$app->request->post('ajax')
            && $model->load(\Yii::$app->request->post())
        ) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        return null;
    }
}