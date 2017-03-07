<?php
namespace backend\traits;

use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: buba
 * Date: 11/19/15
 * Time: 5:02 PM
 */

/**
 * Class Grid
 * @package backend\traits
 *
 */
trait GridTrait
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index' => ['get'],
                    'update' => ['post'],
                ],
            ],
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['update'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ]
            ],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'backend\controllers\actions\ListAction',
                'modelClass' => $this->getModelClass()
            ],
            'update' => [
                'class' => 'backend\controllers\actions\UpdateAction',
                'modelClass' => $this->getModelClass()
            ],
        ];
    }

    abstract function getModelClass();
}