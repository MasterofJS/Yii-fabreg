<?php

use yii\helpers\Html;

/* @var $user common\models\User */
/* @var $post common\models\Post */
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
                            Seu post <b><?= $post->description ?></b> foi denunciado e bloqueado. A violação de nossas regras de postagem podem resultar na terminação de sua conta.
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
