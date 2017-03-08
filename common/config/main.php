<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'Unicorno',
    'timezone' => 'UTC',
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'common\components\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:429',
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:401',
                    ]
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@console/runtime/cache',
            'dirMode' => \common\helpers\FileHelper::FOLDER_MODE,
            'fileMode' => \common\helpers\FileHelper::FILE_MODE,
        ],
        'variables' => [
            'class' => 'common\models\Variable',
        ],
        'frontendUrlManager' => require(__DIR__ .'/frontend-router.php')
    ],

];
