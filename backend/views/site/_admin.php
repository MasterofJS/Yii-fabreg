<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'id' => 'admin-form']); ?>

<?= $form->field($model, 'username', ['template' => "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>{input}</div>{error}"])->textInput(['placeholder' => 'Username']) ?>
<?= $form->field($model, 'password', ['template' => "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-lock\"></i></span>{input}</div>{error}<div id=\"pwindicator\" class=\"pwdindicator\"><div class=\"bar\"></div> <div class=\"pwdstrength-label\"></div> </div>"])->passwordInput(['placeholder' => 'Enter password', 'data-indicator' => 'pwindicator']) ?>
    <div class="row">
        <div class="col-xs-12">
            <?= Html::submitButton(Yii::$app->request->get('edit') ? 'Save' : 'ADD', ['class' => 'btn btn-success col-xs-12', 'name' => 'sign-up-button']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$pwd = Html::getInputId($model, 'password');
$script = <<<JS
    $('#$pwd').pwstrength({
        label: '.pwdstrength-label'
    });
JS;
$this->registerJs($script);
?>