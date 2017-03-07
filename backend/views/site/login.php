<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign in | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;

$script = <<<INLINE
    document.addEventListener('auth', function (e) {
        $('#login-box-inner .alert-danger').show();
    }, false);
INLINE;

$this->registerJs($script);
\backend\assets\BaseAsset::register($this);
?>


<div class="row">
    <div class="col-xs-12">
        <div id="login-box">
            <div id="login-box-holder">
                <div class="row">
                    <div class="col-xs-12">
                        <header id="login-header">
                            <div id="login-logo">
                                <img src="<?= \yii\helpers\Url::to('@public/dist/images/logo.svg') ?>" alt=""/>
                            </div>
                        </header>
                        <div id="login-box-inner">
                            <?php if (Yii::$app->session->hasFlash('error')): ?>
                                <div class="alert alert-danger fade in">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="fa fa-times-circle fa-fw fa-lg"></i>
                                    <strong>Oh snap!</strong> <?= Yii::$app->session->getFlash('error', null, true) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (Yii::$app->session->hasFlash('success')): ?>
                                <div class="alert alert-success fade in">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="fa fa-check-circle fa-fw fa-lg"></i>
                                    <strong>Well
                                        done!</strong> <?= Yii::$app->session->getFlash('success', null, true) ?>
                                </div>
                            <?php endif; ?>
                            <div style="display: none" class="alert alert-danger" role="alert">You are not registered.
                                Please click <a href="<?= \yii\helpers\Url::to(['/site/sign-up']) ?>">here</a> sign up.
                            </div>
                            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['role' => 'form']]); ?>

                            <?= $form->field($model, 'username', ['options' => ['class' => ''], 'template' => "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>{input}</div>{error}"])->textInput(['placeholder' => 'Username']) ?>
                            <?= $form->field($model, 'password', ['options' => ['class' => ''], 'template' => "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-key\"></i></span>{input}</div>{error}"])->passwordInput(['placeholder' => 'Enter password']) ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <?= Html::submitButton('Sign in', ['class' => 'btn btn-success col-xs-12', 'name' => 'sign-up-button']) ?>
                                </div>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
