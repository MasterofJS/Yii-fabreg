<?php
/**@var $model \backend\models\Admin */
/* @var $this yii\web\View */

\backend\assets\BaseAsset::register($this);
\backend\assets\PasswordStrengthAsset::register($this);
$this->title = 'Add Admin';
$this->params['breadcrumbs'][] = ['label' => 'Admins', 'url' => ['admins']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['boxClass'] = 'box-success';
$this->params['col'] = 6;
echo $this->render('_admin', ['model' => $model,]);
