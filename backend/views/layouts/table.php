<?php
/**
 * @var $this yii\web\View
 * @var $content string
 */

?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="row">
    <div class="col-lg-12">

        <div class="row">
            <div class="col-lg-12">
                <?= \yii\widgets\Breadcrumbs::widget([
                    'homeLink' => ['label' => '<i class="fa fa-dashboard"></i> Dashboard', 'url' => Yii::$app->homeUrl],
                    'tag' => 'ol',
                    'encodeLabels' => false,
                    'links' => $this->params['breadcrumbs']
                ]);
                ?>
                <div class="clearfix">
                    <h1 class="pull-left"><?= isset($this->params['header']) ? $this->params['header'] : $this->title ?></h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php if (isset($this->params['toolbar'])): ?>
                                    <?= $this->params['toolbar'] ?>
                                <?php endif ?>
                            </div>
                            <div class="col-lg-6">
                                <?php if (!isset($this->params['per-page']) || $this->params['per-page'] === true): ?>
                                    <form id="per-page-form" data-pjax="#pjax_container"
                                          action="<?= \yii\helpers\Url::current(['per-page' => null]) ?>"
                                          class="input-group input-group-sm pull-right" style="width: 150px;">
                                        <?= \yii\helpers\Html::dropDownList('per-page',
                                            Yii::$app->request->get('per-page', 20),
                                            [
                                                10 => 10,
                                                20 => 20,
                                                50 => 50,
                                                100 => 100,
                                                200 => 200,
                                                500 => 500
                                            ],
                                            [
                                                'class' => 'form-control',
                                                'prompt' => 'Show entries',
                                            ]
                                        ) ?>
                                        <?php $this->registerJs("jQuery('#per-page-form').on('submit change', function(e){ jQuery.pjax.submit(e); })"); ?>
                                    </form>
                                <?php endif ?>
                            </div>
                        </div>


                    </div>
                    <div class="main-box-body clearfix">
                        <?= $content; ?>
                        <?php
                        if (isset($this->params['columns'], $this->params['model'], $this->params['dataProvider'])) {
                            echo \kartik\grid\GridView::widget([
                                'bordered' => false,
                                'striped' => false,
                                'dataProvider' => $this->params['dataProvider'],
                                'filterModel' => $this->params['model'],
                                'filterUrl' => \yii\helpers\ArrayHelper::getValue($this->params, 'filterUrl'),
                                'columns' => $this->params['columns'],
                                'rowOptions' => \yii\helpers\ArrayHelper::getValue($this->params, 'rowOptions'),
                                'tableOptions' => ['class' => 'table user-list table-hover'],
                                'options' => ['class' => 'table-responsive'],
                                'layout' => "{pager}\n{items}\n{pager}",
                                'pjax' => true,
                                'pjaxSettings' => [
                                    'options' => [
                                        'id' => 'pjax_container',
                                        'enablePushState' => false,
                                        'clientOptions' => [
                                            'history' => false,
                                        ],
                                    ],
                                ],
                                'export' => false,
                            ]);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endContent(); ?>

