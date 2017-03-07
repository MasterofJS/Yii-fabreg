<?php
/**
 * @var $this yii\web\View
 * @var $model backend\models\Variable
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $frontendUrlManager yii\web\UrlManager
 */

use backend\models\Variable;
use yii\helpers\Html;

$this->params['breadcrumbs'] = [
    'label' => 'System Variables'
];
$variableAction = !$dataProvider->totalCount ? 'import' : Variable::getNewVariables(require(\Yii::getAlias('@common/data/variables.php'))) ? 'import new' : 'reset';
$this->params['toolbar'] = Html::a('ADD', ['create'], ['class' => 'btn btn-success', 'data-pjax' => 0]) . "\n" .
    Html::a(ucwords($variableAction), [\yii\helpers\Inflector::slug($variableAction)], ['class' => !$dataProvider->totalCount ? 'btn btn-danger' : 'btn btn-warning', 'pjax' => 0, 'id' => 'import',]);
$this->title = 'Settings';
$this->params['header'] = 'System Variables';
$this->params['model'] = $model;
$this->params['dataProvider'] = $dataProvider;
$this->params['columns'] = [

    [
        'attribute' => 'key',
    ],
    [
        'attribute' => 'namespace',
    ],
    [
        'attribute' => 'value',
        'format' => 'raw',
        'value' => function ($model) {
            /**@var $model \backend\models\Variable */
            if ($model->value == 'missing') {
                return \yii\helpers\Html::tag('span', $model->value, [
                    'class' => 'label label-danger'
                ]);
            }
            return $model->value;
        },
    ],
    [
        'attribute' => 'type',
        'format' => 'raw',
        'hAlign' => 'center',
        'filter' => \backend\models\Variable::getTypes(),
        'value' => function ($model) {
            /**@var $model \backend\models\Variable */
            return \yii\helpers\Html::tag('span', $model->getType(), [
                'class' => 'label label-default'
            ]);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => '',
        'mergeHeader' => false,
        'template' => '{update}',
        'buttons' => [
            'update' => function ($url) {
                return Html::a('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-pencil fa-stack-1x fa-inverse"></i></span>', $url, ['class' => 'table-link', 'data-pjax' => 0]);
            },
        ],
    ],
];

$script = <<<JS
    $('#import').on('click', function(e) {
        e.preventDefault();
        var that = $(this);
        var loader = $('<i>').addClass('fa fa-refresh fa-spin');
        $.ajax({
            url: that.attr('href'),
            beforeSend: function() {
                that.prepend(loader);
            },
            complete: function() {
                loader.remove();
            },
            success: function(count) {
                $.pjax.reload('#pjax_container');
            }
        });

    });
JS;

$this->registerJs($script);