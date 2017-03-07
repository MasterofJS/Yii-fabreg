<?php
/**
 * @var $this yii\web\View
 * @var $content string
 */

$col = \yii\helpers\ArrayHelper::getValue($this->params, 'col', 4);

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
            <div class="col-md-<?= (12 - $col) / 2 ?>"></div>
            <div class="col-md-<?= $col ?>">
                <div
                    class="main-box no-header clearfix <?= \yii\helpers\ArrayHelper::getValue($this->params, 'boxClass', '') ?>">
                    <div class="main-box-body clearfix">
                        <?= $content; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-<?= (12 - $col) / 2 ?>"></div>
        </div>
    </div>
</div>

<?php $this->endContent(); ?>
