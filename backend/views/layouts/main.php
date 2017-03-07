<?php
use backend\models\ChangePasswordForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => \yii\helpers\Url::to('@public/static/favicon.ico')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => \yii\helpers\Url::to('@public/static/favicon.ico')]);
\yii\helpers\Url::remember();
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>


        <?php $this->head() ?>
        <style>
            .kv-editable-link {
                border-style: none;
            }

            .editable-click, a.editable-click, a.editable-click:hover {
                text-decoration: none;
                cursor: pointer;
                border-bottom: 0;
            }

            .user-list tbody td > img {
                width: 50px;
                height: 50px;
                border-radius: 50%;
            }

            .notifications-list img {
                border-radius: 50%;
                background-clip: padding-box;
                width: 35px;
            }
        </style>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="md-modal md-effect-1" id="modal-1">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">&times;</button>
                <h4 class="modal-title">Change password</h4>
            </div>
            <div class="modal-body">
                <?php $model = new ChangePasswordForm() ?>
                <?php $form = ActiveForm::begin(
                    [
                        'id' => 'change-password-form',
                        'action' => ['site/change-password'],
                        'enableAjaxValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => false,
                        'validateOnBlur' => false,
                    ]
                ); ?>
                <?= $form->field($model, 'old_password')
                    ->passwordInput()
                ?>
                <?= $form->field($model, 'new_password')
                    ->passwordInput()
                ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#change-password-form').submit();" class="btn btn-primary">Save
                    changes
                </button>
            </div>
        </div>
    </div>
    <div id="theme-wrapper">
        <?php include_once(__DIR__ . '/_header.php') ?>
        <div id="page-wrapper"
             class="container <?= isset($this->params['layout']) && $this->params['layout'] == 'messages' ? 'nav-small' : '' ?>">
            <div class="row">
                <?php include_once(__DIR__ . '/_nav.php') ?>
                <div
                    id="content-wrapper" <?= isset($this->params['layout']) && $this->params['layout'] == 'messages' ? 'class="email-inbox-wrapper"' : '' ?>>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="fa fa-times-circle fa-fw fa-lg"></i>
                            <strong>Oh snap!</strong> <?= Yii::$app->session->getFlash('error', null, true) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="fa fa-check-circle fa-fw fa-lg"></i>
                            <strong>Well done!</strong> <?= Yii::$app->session->getFlash('success', null, true) ?>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                    <?php include_once(__DIR__ . '/_footer.php') ?>
                </div>
            </div>
        </div>

    </div>
    <?php /*include_once(__DIR__.'/_config.php')*/ ?>
    <div class="md-overlay"></div><!-- the overlay element -->

    <?php $this->endBody() ?>
    <script>


        var ModalEffects = (function () {

            function init() {

                var overlay = document.querySelector('.md-overlay');

                [].slice.call(document.querySelectorAll('.md-trigger')).forEach(function (el, i) {

                    var modal = document.querySelector('#' + el.getAttribute('data-modal')),
                        close = modal.querySelector('.md-close');

                    function removeModal(hasPerspective) {
                        classie.remove(modal, 'md-show');

                        if (hasPerspective) {
                            classie.remove(document.documentElement, 'md-perspective');
                        }
                    }

                    function removeModalHandler() {
                        removeModal(classie.has(el, 'md-setperspective'));
                    }

                    el.addEventListener('click', function (ev) {
                        classie.add(modal, 'md-show');
                        overlay.removeEventListener('click', removeModalHandler);
                        overlay.addEventListener('click', removeModalHandler);

                        if (classie.has(el, 'md-setperspective')) {
                            setTimeout(function () {
                                classie.add(document.documentElement, 'md-perspective');
                            }, 25);
                        }
                    });

                    close.addEventListener('click', function (ev) {
                        ev.stopPropagation();
                        removeModalHandler();
                    });

                    //close on escape
                    $(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            e.stopPropagation();
                            removeModalHandler();
                        }
                    });

                });

            }

            init();

        })();
    </script>
    </body>
    </html>
<?php $this->endPage() ?>