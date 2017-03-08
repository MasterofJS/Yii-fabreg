<?php
use yii\web\HttpException;

/* @var $this yii\web\View */
/* @var $exception mixed */

if ($exception instanceof HttpException) {
    $this->registerMetaTag(['name' => 'response:status', 'content' => $exception->statusCode]);
} else {
    $this->registerMetaTag(['name' => 'response:status', 'content' => $exception->getCode()]);
}