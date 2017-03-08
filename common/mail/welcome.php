<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $user common\models\User */
$link = Yii::$app->get('frontendUrlManager')->createAbsoluteUrl(['site/index', 'level1' => 'confirm-email', 'token' => $user->email_confirmation_token]);
?>
<table class="row">
    <tr>
        <td class="wrapper last">
            <table class="twelve columns">
                <tr>
                    <td>

                        <h1>Olá <?= Html::encode($user->first_name) ?></h1>
                        <p class="lead">
                            Você acaba de se registrar na comunidade <?= Yii::$app->name ?>. Para
                            oficializar seu registro clique no link abaixo para ativar sua
                            conta e junte-se a nós.
                        </p>

                    </td>
                    <td class="expander"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="row callout">
    <tr>
        <td class="wrapper last">
            <table class="twelve columns">
                <tr>
                    <td class="panel">
                        <p>Para verificar seu email e ativar sua conta, favor clicar neste
                            link <?= Html::a(Html::encode($link), $link) ?>
                        </p>
                    </td>
                    <td class="expander"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>