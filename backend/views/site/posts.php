<?php

use backend\assets\MagnificPopupAsset;
use backend\models\Post;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * @var $this yii\web\View
 * @var $model Post
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $frontendUrlManager yii\web\UrlManager
 */

MagnificPopupAsset::register($this);
$this->params['breadcrumbs'] = [
    'label' => 'Posts'
];
$this->title = 'Users|' . Yii::$app->name;
$this->params['header'] = 'Posts';
$this->params['model'] = $model;
$this->params['dataProvider'] = $dataProvider;
$this->params['columns'] = [
    [
        'attribute' => 'description',
    ],
    [
        'attribute' => 'username',
        'label' => 'Username',
    ],
    [
        'filter' => [
            Post::CHANNEL_FRESH => 'Fresh',
            Post::CHANNEL_PENDING_TRENDING => 'Trending Up',
            Post::CHANNEL_TRENDING => 'Trending',
            Post::CHANNEL_PENDING_HOT => 'Hot Up',
            Post::CHANNEL_HOT => 'Hot',
        ],
        'attribute' => 'channel',
        'value' => function (Post $model) {
            return ucwords($model->getChannelText());
        },
    ],
    [
        'attribute' => 'is_retired',
        'format' => 'raw',
        'hAlign' => 'center',
        'label' => 'Retired',
        'filter' => [0 => 'No', 1 => 'Yes'],
        'value' => function (Post $model) {
            return \yii\helpers\Html::tag('span', $model->is_retired ? 'yes' : 'no', [
                'class' => $model->is_retired ? 'text-danger' : 'text-success'
            ]);
        },
    ],
    [
        'label' => 'Work',
        'attribute' => 'is_nsfw',
        'format' => 'raw',
        'hAlign' => 'center',
        'filter' => [0 => 'Safe', 1 => 'Unsafe'],
        'value' => function (Post $model) {
            return \yii\helpers\Html::tag('i', '', [
                'class' => $model->is_nsfw ?
                    'fa fa-times text-danger' :
                    'fa fa-check text-success'
            ]);
        },
        'class' => '\kartik\grid\EditableColumn',
        'refreshGrid' => true,
        'editableOptions' => [
            'preHeader' => false,
            'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX,
            'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
            'submitButton' => [
                'icon' => '<i class="fa fa-check"></i>',
                'class' => 'btn btn-sm btn-primary',
            ],
            'resetButton' => [
                'icon' => '<i class="fa fa-ban"></i>',
                'class' => 'btn btn-sm btn-default',
            ],
            'options' => [
                'class' => 'bla',
                'label' => 'NSFW',
            ],
            'afterInput' => function ($form, $widget) {
                /**@var $widget \kartik\editable\Editable * */
                return \yii\helpers\Html::hiddenInput(
                    'editableColumn',
                    preg_replace('/\[\d+\]/', '', $widget->attribute)
                );
            },
            'formOptions' => [
                'action' => ['site/update-post'],
                'method' => 'POST',
            ],
        ]
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'hAlign' => 'center',
        'filter' => ArrayHelper::getcolumn(Post::getStatuses(), 'text'),
        'value' => function (Post $model) {
            return \yii\helpers\Html::tag(
                'span',
                Post::getStatus($model->status, 'text'),
                ['class' => 'label label-' . Post::getStatus($model->status, 'class')]
            );
        },
        'class' => '\kartik\grid\EditableColumn',
        'refreshGrid' => true,
        'editableOptions' => [
            'preHeader' => false,
            'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
            'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
            'data' => ArrayHelper::getcolumn(Post::getStatuses(), 'text'),
            'submitButton' => [
                'icon' => '<i class="fa fa-check"></i>',
                'class' => 'btn btn-sm btn-primary',
            ],
            'resetButton' => [
                'icon' => '<i class="fa fa-ban"></i>',
                'class' => 'btn btn-sm btn-default',
            ],
            'afterInput' => function ($form, $widget) {
                /**@var $widget \kartik\editable\Editable * */
                return \yii\helpers\Html::hiddenInput(
                    'editableColumn',
                    preg_replace('/\[\d+\]/', '', $widget->attribute)
                );
            },
            'editableValueOptions' => [
                'tag' => 'div'
            ],
            'formOptions' => [
                'action' => ['site/update-post'],
                'method' => 'POST',
            ],
        ]
    ],
    [
        'attribute' => 'reports_count',
        'label' => 'Reports',
        'hAlign' => 'center',
        'format' => 'raw',
        'filter' => false,
        'value' => function ($data) {
            return '<a data-pjax=0 href="'
            . \yii\helpers\Url::to(['/reports', 'post_id' => $data->id])
            . '"><span class="badge' . (($data->reports_count > 0) ? ' badge-danger' : '') . '">'
            . $data->reports_count . '</span></a>';
        }
    ],
    [
        'attribute' => 'released_at',
        'filter' => false,
        'format' => 'datetime',
        'label' => 'Release Date',
    ],
    [
        'attribute' => 'created_at',
        'filter' => false,
        'format' => 'date',
        'label' => 'Date',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => '',
        'mergeHeader' => false,
        'template' => '{view} {photo}',
        'buttons' => [
            'view' => function ($url, Post $model) {
                return Html::a(
                    '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i>'
                    .'<i class="fa fa-eye fa-stack-1x fa-inverse"></i></span>',
                    $url,
                    ['class' => 'table-link', 'data-pjax' => 0]
                );
            },
            'photo' => function ($url, Post $model) {
                return Html::a(
                    '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i>'
                    .'<i class="fa fa-file-image-o fa-stack-1x fa-inverse"></i></span>',
                    $model->getPhotoUrl(false),
                    ['class' => 'table-link photo', 'data-pjax' => 0]
                );
            },
        ],
        'urlCreator' => function ($action, Post $model, $key, $index) use ($frontendUrlManager) {
            return $frontendUrlManager->createUrl(['site/index', 'level1' => 'posts', 'level2' => $model->hashId]);
        }
    ],
];

$script = <<<JS
     $('#pjax_container').magnificPopup({
        type: 'image',
        delegate: 'a.photo',
        gallery: {
            enabled: true
        }
     });
JS;

$this->registerJs($script);
?>
