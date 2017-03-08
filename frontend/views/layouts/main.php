<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $user \rest\modules\v1\models\User */

use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;
$this->registerMetaTag(
    [
        'name' => 'user',
        'content' => \yii\helpers\Json::htmlEncode(Yii::$app->user->isGuest ? '' : $user->full())
    ]
);
$this->registerMetaTag(
	[
		'name' => 'cnf',
		'content' => \yii\helpers\Json::encode([
			'descr2' => boolval(Yii::$app->get('variables')->get('posts', 'post.description2'))
		])
	]
);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="no-js" lang="pt">
<head lang="en">
    <meta name="verifyownership" content="7b67d0f53c1698b27e4d995dea523652" />
     <meta http-equiv="pragma" content="no-cache" />
    <meta charset="<?= Yii::$app->charset ?>">
    <!--todo remove-->
    <meta name="environment" content="production">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title)?></title>
    <!--  Begin google site verification  -->
    <meta name="google-site-verification" content="eVGyVZhTvxI3_qKmwOy3quSoTCUqysDm19sz5nRd7bU"/>
    <!--  End google site verification  -->
    <link rel="shortcut icon" href="<?= Url::to('/favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" href="<?= Url::to('/favicon.ico') ?>" type="image/x-icon">
    <link rel="stylesheet"
          href="<?= Url::to('/dist/css/global.min.css') ?>?>"
          type="text/css"/>
    <script async
            src="<?= Url::to('/dist/js/modernizr.js') ?>"></script>
    <?php $this->head() ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-78305725-1', 'auto');
        ga('send', 'pageview');
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<!-- facebook script-->
<div id="fb-root" style="display: none;"></div>

<!-- App container -->
<div id="app_"></div>
<div id="modal_base"></div>
<script src="https://apis.google.com/js/api:client.js"></script>
<script async src="<?= Url::to('/dist/js/main.js') ?>"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
