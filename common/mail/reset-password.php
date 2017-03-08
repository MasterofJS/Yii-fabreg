<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = Yii::$app->get('frontendUrlManager')->createAbsoluteUrl(['site/index', 'level1' => 'reset-password', 'token' => $user->password_reset_token]);
?>

<table class="row">
    <tr>
        <td class="wrapper last">
            <table class="twelve columns">
                <tr>
                    <td>

                        <h1>Olá <?= Html::encode($user->first_name) ?></h1>
                        <p class="lead">Recebemos pedido para mudança de senha em sua conta.
                            Caso você não tenha enviado esse pedido por favor ignore essa
                            mensagem.</p>

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
                        <p>Clique no link abaixo para resetar sua senha:</p>

                        <p><?= Html::a(Html::encode($link), $link) ?></p>
                        <p>Se você estiver recebendo esses emails sem tê-los solicitado, por favor entre em contato conosco em <a href="mailto:Contato@Unicorno.com.br">Contato@Unicorno.com.br</a>, ou use nossa página de contato aqui: <a href="http://unicorno.com.br/contact">Contact page.</a></p>
                    </td>
                    <td class="expander"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>