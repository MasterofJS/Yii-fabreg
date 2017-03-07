<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/25/16
 * Time: 12:02 PM
 */

namespace backend\assets;


use yii\web\AssetBundle;

class DashboardAsset extends AssetBundle
{
    public function init()
    {
        $this->basePath = '@webroot';
        $this->baseUrl = '@web';
        $this->css = [
            'css/libs/morris.css',
            'css/libs/jquery-jvectormap-1.2.2.css',
            'css/libs/hopscotch.css'
        ];
        $this->js = [
            'js/jquery-ui.custom.min.js',
            'js/raphael-min.js',
            'js/morris.min.js',
            'js/jquery-jvectormap-1.2.2.min.js',
            'js/jquery-jvectormap-world-merc-en.js',
            'js/hopscotch.min.js',
            'js/dashboard.js',
        ];
        parent::init();
    }
}