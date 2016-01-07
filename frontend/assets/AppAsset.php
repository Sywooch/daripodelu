<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/reset.css',
        'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300,300italic,400italic,600italic,700italic&subset=latin,cyrillic-ext',
        'https://fonts.googleapis.com/css?family=Cuprum:400,700&subset=latin,cyrillic',
        '/js/bxslider/jquery.bxslider.css',
        '/css/style.css',
    ];
    public $js = [
        '/js/bxslider/jquery.bxslider.min.js',
        '/js/jquery.textarea_autosize.js',
        '/js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
    public $cssOptions = [
        'type' => 'text/css',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];
}
