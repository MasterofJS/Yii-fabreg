<?php
/**@var $user backend\models\Admin */
$user = Yii::$app->user->getIdentity();
?>
<header class="navbar" id="header-navbar">
    <div class="container">
        <a href="<?= \yii\helpers\Url::base() ?>" id="logo" class="navbar-brand">
            <img src="<?= \yii\helpers\Url::to('@public/dist/images/logo.svg') ?>" alt=""
                 class="normal-logo logo-white"/>
            <img src="<?= \yii\helpers\Url::toRoute('img/logo-black.png') ?>" alt="" class="normal-logo logo-black"/>
            <img src="<?= \yii\helpers\Url::toRoute('img/logo-small.png') ?>" alt=""
                 class="small-logo hidden-xs hidden-sm hidden"/>
        </a>

        <div class="clearfix">
            <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="fa fa-bars"></span>
            </button>

            <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                <ul class="nav navbar-nav pull-left">
                    <li>
                        <a class="btn" id="make-small-nav">
                            <i class="fa fa-bars"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-no-collapse pull-right" id="header-nav">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown profile-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs"><?= $user->username ?></span> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#change-password" class="md-trigger" data-modal="modal-1"><i
                                        class="fa fa-refresh"></i>Change password</a></li>
                            <li><a data-method="post" href="<?= \yii\helpers\Url::to(['site/logout']) ?>"><i
                                        class="fa fa-power-off"></i>Logout</a></li>
                        </ul>
                    </li>
                    <li id="logout" class="hidden-xxs">
                        <a data-method="post" href="<?= \yii\helpers\Url::to(['site/logout']) ?>" class="btn">
                            <i class="fa fa-power-off"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>