<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $user common\models\User */
$link = Yii::$app->get('frontendUrlManager')->createAbsoluteUrl(['site/index', 'level1' => 'confirm-email', 'token' => $user->email_confirmation_token]);
?>

<table class="row"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
            align="left" valign="top">
            <table class="twelve columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                        align="left" valign="top">

                        <h1 style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 40px; margin: 0; padding: 0;"
                            align="left">Olá <?= Html::encode($user->first_name) ?></h1>
                        <p class="lead"
                           style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;"
                           align="left">
                            Você acaba de se registrar na comunidade <?= Yii::$app->name ?>. Para
                            oficializar seu registro clique no link abaixo para ativar sua
                            conta e junte-se a nós.
                        </p>

                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="row callout"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 20px;"
            align="left" valign="top">
            <table class="twelve columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td class="panel"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #ECF8FF; margin: 0; padding: 10px; border: 1px solid #b9e5ff;"
                        align="left" bgcolor="#ECF8FF" valign="top">
                        <p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                           align="left">Para verificar seu email e ativar sua conta, favor clicar neste
                            link <?= Html::a(Html::encode($link), $link) ?>
                        </p>
                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
