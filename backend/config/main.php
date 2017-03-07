<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'name' => 'PHPBACKSESSID',
            'sessionTable' => \console\migrations\Migration::TABLE_ADMIN_SESSION
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'baseUrl' => '/backstage',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => false,
                'yii\bootstrap\BootstrapAsset' => false,
                'yii\bootstrap\BootstrapPluginAsset' => false,
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [

                '/' => 'site/index',
                '/login' => 'site/login',
                '/logout' => 'site/logout',
                '/country-statistic' => 'site/country-statistic',
                '/age-statistic' => 'site/age-statistic',
                '/gender-statistic' => 'site/gender-statistic',

                '/admins' => 'site/admins',
                '/admins/delete/<id:\d+>' => 'site/delete-admin',
                '/admins/update/<id:\d+>' => 'site/update-admin',
                '/admins/create' => 'site/create-admin',

                '/users' => 'site/users',
                '/users/delete/<id:\d+>' => 'site/delete-user',
                '/users/update/<id:\d+>' => 'site/update-user',

                '/posts' => 'site/posts',
                '/reports' => 'site/reports',

                '/variables' => 'variable/index',
                '/variables/create' => 'variable/create',
                '/variables/update/<id:\d+>' => 'variable/update',
            ],
        ],

    ],
    'params' => $params,
];
