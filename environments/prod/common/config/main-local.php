<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=unicorno_com_br',
            'username' => 'root',
            'password' => 'f1g4zz0',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 0, //means never expire until you execute [php yii cache/flush-schema]
        ],
        'cloudStorage' => [
            'class' => 'common\components\AmazonS3',
            'key' => 'AKIAJP6CQVSV5DW5DHSA',
            'secret' => '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',
            'bucket' => 'unicorno'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
//            'class' => 'nickcv\mandrill\Mailer',
//            'apikey' => 'YourApiKey',
            'viewPath' => '@common/mail',
            
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
