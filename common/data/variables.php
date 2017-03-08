<?php
return [
    'promotion' => [
        'weight.share' => [
            'value' => 2,
            'type' => 'integer',
        ],
        'weight.comment' => [
            'value' => 2,
            'type' => 'integer',
        ],
        'weight.like' => [
            'value' => 1,
            'type' => 'integer',
        ],
        'score.trending' => [
            'value' => 1,
            'type' => 'integer',
        ],
        'score.hot' => [
            'value' => 1,
            'type' => 'integer',
        ],
        'interval.trending' => [
            'value' => 24,
            'type' => 'hour',
        ],
        'interval.hot' => [
            'value' => 24,
            'type' => 'hour',
        ],
        'percentage.trending' => [
            'value' => 100,
            'type' => 'percentage',
        ],
        'percentage.hot' => [
            'value' => 100,
            'type' => 'percentage',
        ],
        'limit.default' => [
            'value' => 2,
            'type' => 'integer',
        ],

    ],
    'retirement' => [
        'interval' => [
            'value' => 72,
            'type' => 'hour',
        ]
    ],
    'email' => [
        'noreply' => [
            'value' => 'noreply@unicorno.com',
            'type' => 'email',
        ],
        'support' => [
            'value' => 'support@unicorno.com',
            'type' => 'email',
        ],
        'contact' => [
            'value' => 'contact@unicorno.com',
            'type' => 'email',
        ],
    ],
    'pageSize' => [
        'featured.posts' => [
            'value' => 60,
            'type' => 'integer',
        ],
    ],
    'login' => [
        'remember' => [
            'value' => 24,
            'type' => 'hour',
        ],
    ],
    'token' => [
        'password' => [
            'value' => 1,
            'type' => 'hour',
        ],
        'email' => [
            'value' => 1,
            'type' => 'hour',
        ],
    ],
    'social' => [
        'facebook_link' => [
            'value' => 'http://facebook.com',
            'type' => 'url'
        ],
        'twitter_link' => [
            'value' => 'http://twitter.com',
            'type' => 'url'
        ],
        'facebook.app_id' => [
            'value' => 'app_id',
            'type' => 'string'
        ],
        'facebook.app_secret' => [
            'value' => 'app_secret',
            'type' => 'string'
        ],
        'google.client_id' => [
            'value' => 'client_id',
            'type' => 'string'
        ],
        'google.client_secret' => [
            'value' => 'client_secret',
            'type' => 'string'
        ],
    ],
    'api.usage' => [
        'v1.post.create' => [
            'value' => '1/360',
            'type' => 'string'
        ],
    ],
    'cleaner' => [
        'temp.files' => [
            'value' => 1,
            'type' => 'hour'
        ],
        'notifications' => [
            'value' => 72,
            'type' => 'hour'
        ],
    ],
		'posts' => [
			'post.description2' => [
				'value' => 1,
				'type' => 'integer'
			],
    ]

];
