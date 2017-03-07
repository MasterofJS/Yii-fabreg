<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-rest',
    'language' => 'pt',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'rest\controllers',
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'rest\modules\v1\Module'
        ]
    ],
    'components' => [
        'user' => [
            'class' => 'rest\modules\v1\components\User',
            'identityClass' => 'rest\modules\v1\models\User',
            'enableAutoLogin' => true,
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'name' => 'PHPFRONTSESSID',
        ],
        'request' => [
            'baseUrl' => '/api',
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                /**
                 * @var \yii\web\Response $response
                 */
                $response = $event->sender;
                if (!$response->isSuccessful) {
                    $response->format = \yii\web\Response::FORMAT_HTML;
                    if ($response->data && !empty($response->data['message'])) {
                        $response->data = $response->data['message'];
                    } else {
                        $response->data = $response->statusText;
                    }
                }
            },
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/oauth',
                    'pluralize' => false,
                    'tokens' => [
                        '{provider}' => '<provider:(google|facebook)>',
                    ],
                    'patterns' => [
                        'POST facebook' => 'facebook',
                        'POST google' => 'google',
                        'DELETE {provider}' => 'disconnect',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/auth',
                    'pluralize' => false,
                    'tokens' => [
                    ],
                    'patterns' => [
                        'POST sign-up' => 'sign-up',
                        'POST login' => 'login',
                        'POST logout' => 'logout',
                        'POST facebook' => 'facebook',
                        'POST google' => 'google',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/upload',
                    'pluralize' => false,
                    'patterns' => [
                        'POST' => 'photo',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/media',
                    'pluralize' => false,
                    'patterns' => [
                        'GET,HEAD default-avatars' => 'default-avatars',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/recovery',
                    'pluralize' => false,
                    'patterns' => [
                        'PUT password/reset' => 'reset',
                        'PUT password/request' => 'request',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/account',
                    'pluralize' => false,
                    'patterns' => [
                        'PUT email/confirm' => 'activation',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'tokens' => [
                        '{id}' => '<username:[a-z][0-9a-z-]+>',
                        '{page}' => '<page:\\d+>',
                    ],
                    'patterns' => [
                        'GET,HEAD me' => 'me',
                        'PUT me' => 'update',
                        'PUT me/profile' => 'profile',
                        'PUT me/settings' => 'settings',
                        'PUT me/change-password' => 'change-password',
                        'PUT me/confirm-email' => 'confirm-email',
                        'DELETE me/delete' => 'delete',
                        'GET,HEAD {id}' => 'view',
                        'POST {id}/mute' => 'mute',
                        'DELETE {id}/unmute' => 'unmute',
                        'GET,HEAD {id}/feed/{page}' => 'feed',
                        'GET,HEAD {id}/feed' => 'feed',
                        'GET,HEAD {id}/posts/{page}' => 'posts',
                        'GET,HEAD {id}/posts' => 'posts',
                        'GET,HEAD {id}/comments/{page}' => 'comments',
                        'GET,HEAD {id}/comments' => 'comments',
                        'GET,HEAD {id}/likes/{page}' => 'likes',
                        'GET,HEAD {id}/likes' => 'likes',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/post',
                    'tokens' => [
                        '{id}' => '<id:[a-zA-Z0-9]+>',
                        '{page}' => '<page:\\d+>',
                        '{filter}' => '<filter:trending|fresh|hot>',
                    ],
                    'patterns' => [
                        'GET,HEAD page/{page}' => 'index',
                        'GET,HEAD' => 'index',
                        'GET,HEAD {filter}/page/{page}' => 'index',
                        'GET,HEAD {filter}' => 'index',
                        'GET,HEAD {id}/comments/page/{page}' => 'comments',
                        'GET,HEAD {id}/comments' => 'comments',
                        'GET,HEAD {id}/comments/{filter}/page/{page}' => 'comments',
                        'GET,HEAD {id}/comments/{filter}' => 'comments',
                        'POST' => 'create',
                        'DELETE {id}' => 'delete',
                        'PUT {id}' => 'update',
                        'GET,HEAD {id}' => 'view',
                        'POST {id}/like' => 'like',
                        'POST {id}/dislike' => 'dislike',
                        'POST {id}/report' => 'report',
                        'POST {id}/share' => 'share',
                    ],
                    'extraPatterns' => [
                        'GET featured' => 'featured',
                        'GET search' => 'search',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/comment',
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{filter}' => '<filter:fresh|hot>',
                    ],
                    'patterns' => [
                        'GET,HEAD {id}/comments' => 'index',
                        'GET,HEAD {id}/comments/{filter}' => 'index',
                        'POST' => 'create',
                        'DELETE {id}' => 'delete',
                        'GET,HEAD {id}' => 'view',
                        'POST {id}/like' => 'like',
                        'POST {id}/dislike' => 'dislike',
                        'POST {id}/report' => 'report',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/notification',
                    'tokens' => [
                    ],
                    'patterns' => [
                        'GET,HEAD' => 'index',
                        'GET,HEAD get-unread' => 'get-unread',
                        'PUT read-all' => 'read-all',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/contact',
                    'pluralize' => false,
                    'tokens' => [
                    ],
                    'patterns' => [
                        'POST' => 'send-form',
                    ],
                ],

            ],
        ]
    ],
    'params' => $params,
];


