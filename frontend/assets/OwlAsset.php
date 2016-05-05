<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class OwlAsset extends AssetBundle
{
    public $sourcePath = '@bower/owlcarousel/owl-carousel';
    public $css = [
        'owl.carousel.css',
        'owl.theme-1.31.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public function registerAssetFiles($view)
    {
        $this->js[] = 'owl.carousel' . (!YII_DEBUG ? '.min' : '') . '.js';
        parent::registerAssetFiles($view);
    }
}