<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/24/15
 * Time: 4:53 PM
 */

namespace backend\controllers\actions;


use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class DetailAction extends Action
{
    /**
     * @var string
     */
    public $modelClass;
    /**
     * @var ActiveRecord
     */
    private $model;

    public function run()
    {
        $request = \Yii::$app->request;
        $primaryKey = (array)call_user_func([$this->modelClass, 'primaryKey']);
        $this->model = call_user_func([$this->modelClass, 'find'])->where(array_combine($primaryKey, (array)$request->post('expandRowKey')))->one();
        if ($this->model) {
            return $this->controller->renderPartial('_detail', ['model' => $this->model]);
        } else {
            return Html::tag('div', '<i class="icon fa fa-ban"></i> Not Found', ['class' => 'alert alert-danger']);
        }
    }


}