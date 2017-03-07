<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Variable */

$this->title = 'Create Variable';
$this->params['breadcrumbs'][] = ['label' => 'Variables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['boxClass'] = 'box-success';
$this->params['col'] = 6;


echo $this->render('_form', ['model' => $model,]);






