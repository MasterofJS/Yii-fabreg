<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/19/15
 * Time: 4:47 PM
 */

namespace backend\controllers\actions;


use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class UpdateAction extends Action
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
        $this->model = \Yii::createObject($this->modelClass);
        $primaryKey = (array)call_user_func([$this->modelClass, 'primaryKey']);
        $keys = $request->post('editableKey');
        if (count($primaryKey) > 1 && !is_array($keys)) {
            $keys = Json::decode($keys);
        } else {
            $keys = (array)$keys;
        }

        if (ArrayHelper::isIndexed($keys)) {
            $keys = array_combine($primaryKey, $keys);
        }
        $this->model = call_user_func([$this->modelClass, 'find'])->andWhere($keys)->one();
        $this->model->setScenario('update');
        $models = [];
        $models[$request->post('editableIndex')] = $this->model;
        if ($this->model && call_user_func([$this->modelClass, 'loadMultiple'], $models, $request->post())) {
            $response = [];
            if (!$this->model->update()) {
                $response['message'] = $this->model->getFirstError($request->post('editableColumn'));
            }
            return $response;
        }
        throw new BadRequestHttpException();
    }

}