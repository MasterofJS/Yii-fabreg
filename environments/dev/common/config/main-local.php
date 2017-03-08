<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'cloudStorage' => [
            'class' => 'common\components\AmazonS3',
            'key' => 'AKIAJP6CQVSV5DW5DHSA',
            'secret' => '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',
            'bucket' => 'unicorno'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'rest\modules\v1\components\Facebook',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
                'google' => [
                    'class' => 'rest\modules\v1\components\GoogleOAuth',
                    'clientId' => '',
                    'clientSecret' => '',
                ],

            ],
        ],
    ],
];
