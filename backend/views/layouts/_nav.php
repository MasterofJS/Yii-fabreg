<?php
$action = Yii::$app->controller->action->getUniqueId()
?>
<div id="nav-col">
    <section id="col-left" class="col-left-nano">
        <div id="col-left-inner" class="col-left-nano-content">
            <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                <ul class="nav nav-pills nav-stacked">
                    <li class="<?= $action == 'site/index' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::base() ?>/">
                            <i class="fa fa-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="<?= in_array($action, ['site/admins', 'site/users']) ? 'open active' : ''; ?>">
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-users"></i>
                            <span>Users</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="<?= \yii\helpers\Url::to(['site/admins']) ?>"
                                   class="<?= $action == 'site/admins' ? 'active' : '' ?>">
                                    Administrators
                                </a>
                            </li>
                            <li>
                                <a href="<?= \yii\helpers\Url::to(['site/users']) ?>"
                                   class="<?= $action == 'site/users' ? 'active' : '' ?>">
                                    Members
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?= $action == 'site/posts' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['site/posts']) ?>">
                            <i class="fa fa-picture-o"></i>
                            <span>Posts</span>
                        </a>
                    </li>
                    <li class="<?= in_array($action, ['variable/index', 'variable/create', 'variable/update']) ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['variable/index']) ?>">
                            <i class="fa fa-cogs"></i>
                            <span>Variables</span>
                        </a>
                    </li>
                    <li class="<?= $action == 'site/reports' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['site/reports']) ?>">
                            <i class="fa fa-fire"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>