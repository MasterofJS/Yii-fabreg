<?php
/**
 * @var $this yii\web\View
 * @var $model backend\models\User
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $frontendUrlManager yii\web\UrlManager
 */

use backend\models\Admin;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->params['breadcrumbs'] = [
    'label' => 'Users'
];
$this->title = 'Users|' . Yii::$app->name;
$this->params['header'] = 'Admins';
$this->params['model'] = $model;
$this->params['dataProvider'] = $dataProvider;
$this->params['columns'] = [

    [
        'attribute' => 'username',
    ],
    [
        'attribute' => 'last_login',
        'format' => 'datetime',
        'label' => 'Last Login',
    ],
    [
        'attribute' => 'created_at',
        'format' => 'date',
        'label' => 'Date',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'mergeHeader' => false,
        'header' => '',
        'template' => '{delete}',
        'buttons' => [
            'delete' => function ($url, Admin $model) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash-o fa-stack-1x fa-inverse"></i></span>', $url, ['class' => 'table-link danger', 'data-confirm' => 'Are you sure you want to delete this admin?', 'data-method' => 'post', 'data-pjax' => '0',]);
            },
        ],
        'visibleButtons' => [
            'delete' => function (Admin $model) {
                return $model->id != Yii::$app->user->id && strcmp('admin', $model->username);
            }
        ],
        'urlCreator' => function ($action, Admin $model) {
            return \yii\helpers\Url::to(['site/delete-admin', 'id' => $model->id]);
        }
    ],
];
$this->params['toolbar'] = Html::a('ADD', ['create-admin'], ['class' => 'btn btn-success', 'data-pjax' => 0]);
