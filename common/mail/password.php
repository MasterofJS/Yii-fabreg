<table class="row">
    <tr>
        <td class="wrapper last">
            <table class="twelve columns">
                <tr>
                    <td>

                        <h1>Olá <?= yii\helpers\Html::encode($user->first_name) ?></h1>
                        <p class="lead">
                            Fala <?= yii\helpers\Html::encode($user->first_name) ?>, Você se registrou no site Unicorno. Aqui está a senha para sua conta: <?= $password ?><br/>Guarde com cuidado.
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