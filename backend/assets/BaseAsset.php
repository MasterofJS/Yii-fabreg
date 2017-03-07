<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseAsset extends AssetBundle
{
    public function init()
    {
        $this->basePath = '@webroot';
        $this->baseUrl = '@web';
        $this->css = [
            'css/bootstrap/bootstrap.min.css',
            'css/libs/font-awesome.css',
            'css/libs/nanoscroller.css',
            'css/compiled/theme_styles.css',
            //'css/libs/datepicker.css',
            'css/libs/nifty-component.css',
            '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400',
        ];
        $this->js = [
            //'js/demo-rtl.js',
            //'js/demo-skin-changer.js',
            'js/jquery.js',
            'js/bootstrap.js',
            'js/jquery.nanoscroller.min.js',
            //'js/demo.js',
            //'js/bootstrap-datepicker.js',
            'js/modernizr.custom.js',
            'js/classie.js',
            'js/scripts.js',
            'js/pace.min.js',
        ];
        parent::init();
    }
}
