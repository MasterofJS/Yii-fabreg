<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Variable */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'id' => 'variable-form']); ?>

<?= $form->field($model, 'namespace')->textInput() ?>

<?= $form->field($model, 'key')->textInput() ?>

<?= $form->field($model, 'type')->dropDownList(\backend\models\Variable::getTypes()) ?>

<?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$id = $form->getId();
$script = <<<JS
    $('#$id').on('afterValidateAttribute', function(e, attribute, messages) {

        var _form = $(this),_input,
        hasError = messages.length > 0;
        _input = _form.find(attribute.input);
        if (_input.length && _input[0].tagName.toLowerCase() === 'div') {
            _input = _input.find('input');
        }
        if (_input.length) {
            var _container = _form.find(attribute.container);
            var _icon = $('<i class="fa">'), _label = _container.find('label');
            if(_label.length){
                if (hasError) {
                   _icon.addClass('fa-times-circle-o')
                } else {
                   _icon.addClass('fa-check')
                }
                _label.html(_label.text()).prepend(' ').prepend(_icon);
            }
        }
    });
JS;
$this->registerJs($script);
?>