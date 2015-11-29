<?php

namespace backend\widgets\grid;

use yii\web\AssetBundle;


class TreeGridAssets extends AssetBundle {

    public $sourcePath = '@backend/widgets/yii2-widget-treegrid/assets';

    public $css = [
        'css/jquery.treegrid.css',
    ];

    public $js = [
        'js/jquery.treegrid.js',
        'js/jquery.treegrid.bootstrap3.js',
    ];

    public $depends = [
        'yii\grid\GridViewAsset',
    ];
}