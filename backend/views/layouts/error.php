<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>

        <!-- bootstrap -->
        <link rel="stylesheet" type="text/css" href="<?= Url::base() ?>/css/bootstrap/bootstrap.min.css"/>

        <!-- RTL support - for demo only -->
        <script src="<?= Url::base() ?>/js/demo-rtl.js"></script>
        <!--
        If you need RTL support just include here RTL CSS file <link rel="stylesheet" type="text/css" href="css/libs/bootstrap-rtl.min.css" />
        And add "rtl" class to <body> element - e.g. <body class="rtl">
        -->

        <!-- libraries -->
        <link rel="stylesheet" type="text/css" href="<?= Url::base() ?>/css/libs/font-awesome.css"/>

        <!-- global styles -->
        <link rel="stylesheet" type="text/css" href="<?= Url::base() ?>/css/compiled/theme_styles.css"/>

        <!-- this page specific styles -->

        <!-- google font libraries -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400'
              rel='stylesheet' type='text/css'>

        <link type="image/x-icon" href="/images/favicon.ico" rel="shortcut icon"/>

        <!--[if lt IE 9]>
        <script src="<?=Url::base()?>/js/html5shiv.js"></script>
        <script src="<?=Url::base()?>/js/respond.min.js"></script>
        <![endif]-->
        <?php $this->head() ?>
    </head>
    <body id="error-page">
    <?php $this->beginBody() ?>
    <div class="container">
        <?= $content ?>
    </div>

    <!-- global scripts -->
    <script src="<?= Url::base() ?>/js/jquery.js"></script>
    <script src="<?= Url::base() ?>/js/bootstrap.js"></script>

    <!-- this page specific scripts -->


    <!-- theme scripts -->
    <script src="<?= Url::base() ?>/js/scripts.js"></script>

    <!-- this page specific inline scripts -->

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>