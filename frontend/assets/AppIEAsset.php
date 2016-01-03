<?php

namespace frontend\assets;

use yii\web\AssetBundle;


class AppIEAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/html5shiv.js',
        '/js/respond.min.js',
    ];

    public $jsOptions = [
        'condition' => 'lt IE 9',
        'position' => \yii\web\View::POS_END,
    ];
}