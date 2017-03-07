<?php
use backend\models\Post;
use backend\models\Report;
use backend\models\User;
use common\models\Media;

/* @var $this yii\web\View */
/* @var $frontendUrlManager yii\web\UrlManager */


$this->title = Yii::$app->name;
\backend\assets\DashboardAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li class="active"><span>Dashboard</span></li>
                </ol>
                <h1>Dashboard</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="main-box infographic-box">
                    <i id="active-users" class="fa fa-user green-bg"></i>
                    <span class="headline">Users</span>
                    <span class="value"><?= User::count(); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="main-box infographic-box">
                    <i id="active-posts" class="fa fa-picture-o emerald-bg"></i>
                    <span class="headline">Posts</span>
                    <span class="value"><?= Post::count(); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="main-box infographic-box">
                    <i id="pending-reports" class="fa fa-fire red-bg"></i>
                    <span class="headline">Reports</span>
                    <span class="value"><?= Report::count(); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="main-box infographic-box">
                    <i id="active-hot-posts" class="fa fa-h-square yellow-bg"></i>
                    <span class="headline">Hot</span>
                    <span class="value"><?= Post::hot(); ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="main-box">
                    <header class="main-box-header clearfix">
                        <h2>Age Statistics</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div id="hero-bar"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="main-box">
                    <header class="main-box-header clearfix">
                        <h2>Gender Statistics</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div id="hero-donut"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="main-box">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">Country Statistics</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div id="world-map" style="width: 100%; height: 400px"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="main-box">
                    <div class="tabs-wrapper tabs-no-header">
                        <ul class="nav nav-tabs">
                            <li id="latest-users" class="active"><a href="#tab-users" data-toggle="tab">Users</a></li>
                            <li id="latest-posts"><a href="#tab-products" data-toggle="tab">Posts</a></li>
                        </ul>
                        <div class="tab-content tab-content-body clearfix">
                            <div class="tab-pane fade in active" id="tab-users">
                                <ul class="widget-users row">
                                    <?php foreach (User::lastRegistered() as $user): ?>
                                        <li class="col-md-6">
                                            <div class="img">
                                                <img style="height: 50px; width: 50px"
                                                     src="<?= $user->avatar->getUrl() ?>" alt=""/>
                                            </div>
                                            <div class="details">
                                                <div class="name">
                                                    <a href="<?= $frontendUrlManager->createUrl(['site/index', 'level1' => 'user', 'level2' => $user->username]) ?>"><?= $user->name ?></a>
                                                </div>
                                                <div class="time">
                                                    <i class="fa fa-clock-o"></i> Member
                                                    since: <?= Yii::$app->formatter->asDate($user->created_at) ?>
                                                </div>
                                                <div class="type">
                                                    <span
                                                        class="label label-<?= User::getStatus($user->status, 'class'); ?>"><?= User::getStatus($user->status, 'text') ?></span>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="tab-products">
                                <ul class="widget-products">
                                    <?php foreach (Post::lastPublished() as $post): ?>
                                        <li>
                                            <a href="<?= $frontendUrlManager->createUrl(['site/index', 'level1' => 'posts', 'level2' => $post->hashId]) ?>">
                                            <span class="img">
                                                <img src="<?= $post->photo->getUrl(true, Media::TYPE_NI) ?>" alt=""/>
                                            </span>
                                            <span class="product clearfix">
                                                <span class="name">
                                                    <?= $post->description ?>
                                                </span>
                                                <span class="price">
                                                    <i class="fa fa-thumbs-up"></i> <?= $post->likes ?>
                                                </span>
                                                <span class="warranty">
                                                    <i class="fa fa-comment"
                                                       aria-hidden="true"></i> <?= $post->comments ?>
                                                </span>
                                            </span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
