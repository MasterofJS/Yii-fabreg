<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \backend\models\Variable */

$this->title = 'Update Variable: ' . '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Variables', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Variable#' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['boxClass'] = 'box-primary';

echo $this->render('_form', ['model' => $model,]);


