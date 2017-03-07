<?php

use backend\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model User
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $frontendUrlManager yii\web\UrlManager
 */

$this->params['breadcrumbs'] = [
    'label' => 'Users'
];
$this->title = 'Users|' . Yii::$app->name;
$this->params['header'] = 'Users';
$this->params['model'] = $model;
$this->params['dataProvider'] = $dataProvider;
$this->params['columns'] = [
    [
        'format' => 'Html',
        'value' => function (User $user) {
            $returnVal = '';
            $returnVal .= Html::img($user->avatar->getUrl(false), ['style' => 'border-radius:50%']);
            return $returnVal;
        },
    ],
    [
        'attribute' => 'username',
    ],
    [
        'attribute' => 'name',
        'label' => 'Name',
    ],
    [
        'attribute' => 'email',
    ],
    [
        'attribute' => 'country',
        'value' => function (User $model) {
            return $model->country ? \common\models\UserForm::getCountries()[$model->country] : null;
        },
        'filter' => \common\models\UserForm::getCountries(),
    ],
    [
        'attribute' => 'gender',
        'filter' => [User::GENDER_MALE => 'Male', User::GENDER_FEMALE => 'Female', User::GENDER_OTHER => 'Other'],
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'hAlign' => 'center',
        'filter' => ArrayHelper::getcolumn(User::getStatuses(), 'text'),
        'value' => function (User $model) {
            return Html::tag('span', User::getStatus($model->status, 'text'), ['class' => 'label label-' . User::getStatus($model->status, 'class')]);

        },
        'class' => '\kartik\grid\EditableColumn',
        'refreshGrid' => true,
        'editableOptions' => [
            'preHeader' => false,
            'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
            'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
            'data' => ArrayHelper::getcolumn(User::getStatuses(), 'text'),
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
                'action' => ['site/update-user'],
                'method' => 'POST',
            ],
        ]
    ],
    [
        'attribute' => 'birthday',
        'format' => 'date',
        'filter' => false,
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
        'template' => '{view}{reason}',
        'buttons' => [
            'reason' => function ($url, User $model) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-question-circle fa-stack-1x fa-inverse"></i></span>', 'javascript:void(0);', ['data-content' => $model->deletion_reason, 'data-toggle' => 'popover', 'data-placement' => 'left', 'class' => 'deletion_reason table-link danger']);
            },
            'view' => function ($url, User $model) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-eye fa-stack-1x fa-inverse"></i></span>', $url, ['class' => 'table-link', 'data-pjax' => 0]);
            },
        ],
        'visibleButtons' => [
            'reason' => function (User $model) {
                return $model->isDeleted() && !empty($model->deletion_reason);
            },
        ],
        'urlCreator' => function ($action, User $model) use ($frontendUrlManager) {
            return $frontendUrlManager->createUrl(['site/index', 'level1' => 'user', 'level2' => $model->username]);
        }
    ],
];
$script = <<<JS
      $('#pjax_container .deletion_reason').popover();
JS;

$this->registerJs($script);
