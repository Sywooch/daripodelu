<?php

namespace frontend\assets;

use yii\web\AssetBundle;


class Select2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/js/select2/dist/css/select2.css',
    ];

    public $js = [
        '/js/select2/dist/js/select2.full.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
}