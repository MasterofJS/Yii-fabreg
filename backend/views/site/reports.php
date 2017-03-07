<style>
    #multi-actions {
        display: none;
    }

    #multi-actions > .row {
        padding-bottom: 5px;
        width: 110px;
    }

    #multi-actions > .row > .btn {
        width: 100%;
    }
</style>
<?php

use backend\assets\MagnificPopupAsset;
use backend\models\Report;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * @var $this yii\web\View
 * @var $model Report
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $frontendUrlManager yii\web\UrlManager
 */

MagnificPopupAsset::register($this);
$this->params['breadcrumbs'] = [
    'label' => 'Reports'
];
$this->title = 'Users|' . Yii::$app->name;
$this->params['header'] = 'Reports';
$this->params['model'] = $model;
$this->params['dataProvider'] = $dataProvider;
$this->params['columns'] = [
    [
        'format' => 'raw',
        'value' => function ($data) {
            return $data->isPending() ? Html::checkbox('select-all', false, [
                'class' => 'select-row',
                'data-user-id' => $data->user_id,
                'data-post-id' => $data->post_id
            ]) : '';
        },
        'header' => Html::checkbox('select-row', false, [
            'class' => 'select-all',
        ]),
        'filter' => '<div id="multi-actions">
                        <div class="row"><a href="' . \yii\helpers\Url::to(['site/multi-report-update', 'status' => Report::STATUS_DISAPPROVED]) . '" class="btn btn-danger multi-change-status">Ban post</a></div>
                        <div class="row"><a href="' . \yii\helpers\Url::to(['site/multi-report-update', 'status' => Report::STATUS_APPROVED]) . '"class="btn btn-success multi-change-status">Keep Post</a></div>
                     </div>',
    ],
    [
        'format' => 'raw',
        'label' => 'Post Photo',
        'value' => function (Report $model) {
            return Html::a('<i class="fa fa-file-image-o"></i>', $model->post->getPhotoUrl(false), ['class' => 'photo']);
        },
    ],
    [
        'attribute' => 'postDesc',
        'label' => 'Post Desc',
    ],
    [
        'attribute' => 'type',
        'value' => function ($data) {
            switch ($data->type) {
                case Report::TYPE_COPYRIGHT:
                    return 'Violando direitos autorais';
                case Report::TYPE_SPAM:
                    return 'Spam, propaganda';
                case Report::TYPE_OFFENSIVE:
                    return 'Material ofensivo/nudez';
            }
        }
    ],
    [
        'attribute' => 'username',
        'label' => 'Username',
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'hAlign' => 'center',
        'filter' => ArrayHelper::getcolumn(Report::getStatuses(), 'text'),
        'value' => function (Report $model) {
            return \yii\helpers\Html::tag('span', Report::getStatus($model->status, 'text'), ['class' => 'label label-' . Report::getStatus($model->status, 'class')]);
        },
        'class' => '\kartik\grid\EditableColumn',
        'refreshGrid' => true,
        'readonly' => function (Report $model) {
            return !$model->isPending();
        },
        'editableOptions' => [
            'preHeader' => false,
            'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
            'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
            'data' => ArrayHelper::getcolumn(Report::getStatuses(), 'text'),
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
                return \yii\helpers\Html::hiddenInput('editableColumn', preg_replace('/\[\d+\]/', '', $widget->attribute));
            },
            'editableValueOptions' => [
                'tag' => 'div'
            ],
            'formOptions' => [
                'action' => ['site/update-report'],
                'method' => 'POST',
            ],
        ]
    ],
    [
        'attribute' => 'created_at',
        'filter' => false,
        'format' => 'datetime',
        'label' => 'Date',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'width' => '10%',
        'header' => '',
        'mergeHeader' => false,
        'template' => '{post}{user}',
        'buttons' => [
            'post' => function ($url) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-rss fa-stack-1x fa-inverse"></i></span>', $url, ['class' => 'table-link', 'data-pjax' => 0]);
            },
            'user' => function ($url) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-user fa-stack-1x fa-inverse"></i></span>', $url, ['class' => 'table-link', 'data-pjax' => 0]);
            },
        ],
        'urlCreator' => function ($action, Report $model) use ($frontendUrlManager) {
            switch ($action) {
                case 'user':
                    return $frontendUrlManager->createUrl(['site/index', 'level1' => 'user', 'level2' => $model->username]);
                case 'post':
                    return $frontendUrlManager->createUrl(['site/index', 'level1' => 'posts', 'level2' => $model->post->hashId]);
            }
            return null;
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

     function multi_controls()
     {
        if($('input.select-row:checked').length > 1){
            $('#multi-actions').show()
        } else {
            $('#multi-actions').hide()
        }
     }

     $(document).on('change', 'input.select-all', (function(){
        $('input.select-row').prop('checked', $(this).is(':checked'));
        multi_controls();
     })).on('click', '.multi-change-status', function(event){
        event.preventDefault();
        var reports = [];
        $('input.select-row:checked').each(function(key, input){
            reports.push({user_id: $(input).data('user-id'), post_id: $(input).data('post-id')})
        });
        $.post($(this).attr('href'), {params: JSON.stringify(reports)}, function(data){
            location.reload();
        })
     });
JS;

$this->registerJs($script);
?>