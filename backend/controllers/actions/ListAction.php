<?php
namespace backend\controllers\actions;

/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/19/15
 * Time: 4:37 PM
 */

use \yii\base\Action;
use yii\db\ActiveRecord;

class ListAction extends Action
{
    public $layout = 'table';
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
        $this->controller->layout = $this->layout;
        $this->model = \Yii::createObject($this->modelClass, [['scenario' => 'search']]);
        $dataProvider = $this->model->search(\Yii::$app->request->queryParams);    
        return $this->controller->render($this->id, ['model' => $this->model, 'dataProvider' => $dataProvider]);
    }

}