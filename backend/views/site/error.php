<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception yii\base\Exception */

$this->title = $name;
$this->context->layout = 'error';
?>

<div class="row">
    <div class="col-xs-12">
        <div id="error-box">
            <div class="row">
                <div class="col-xs-12">
                    <?php if (404 == $exception->statusCode): ?>
                        <div id="error-box-inner">
                            <img src="<?= Url::base() ?>/img/error-404-v3.png" alt="Have you seen this page?"/>
                        </div>
                        <h1>ERROR 404</h1>
                        <p>
                            Page not found.<br/>
                            If you find this page, let us know.
                        </p>
                    <?php elseif (500 == $exception->statusCode): ?>

                        <div id="error-box-inner">
                            <img src="<?= Url::base() ?>/img/error-500-v1.png" alt="Error 500"/>
                        </div>
                        <h1>ERROR 500</h1>
                        <p>
                            Something went very wrong. We are sorry for that.
                        </p>
                    <?php else: ?>
                        <h1>ERROR <?= $exception->statusCode ?></h1>
                        <p><?= nl2br(Html::encode($message)) ?></p>
                    <?php endif; ?>
                    <p>
                        Go back to <a href="/">homepage</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
