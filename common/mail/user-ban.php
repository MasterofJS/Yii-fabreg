<?php

use yii\helpers\Html;

/* @var $user common\models\User */
/* @var $this yii\web\View */

?>
<table class="row">
    <tr>
        <td class="wrapper last">
            <table class="twelve columns">
                <tr>
                    <td>

                        <h1>Olá <?= Html::encode($user->first_name) ?></h1>
                        <p class="lead">
                            Você violou nossos termos de serviço e por isso sua conta foi apagada. Unicorno é um portal de diversão e amizade. Ações tomadas em sua conta não condizem com as regras de nosso site. Tchau tchau.
                        </p>
                        <p>
                            Unicorno
                        </p>

                    </td>
                    <td class="expander"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>