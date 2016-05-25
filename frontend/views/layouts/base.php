<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\models\MenuTree;
use frontend\assets\AppAsset;
use frontend\assets\AppIEAsset;
use frontend\widgets\LastNewsWidget;
use newerton\fancybox\FancyBox;

/* @var $this \yii\web\View */
/* @var $content string */

AppIEAsset::register($this);
AppAsset::register($this);

$menu = new MenuTree(Yii::$app->cache);
$menuItems = $menu->getMenuItems();

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <?php /*
    <!--<link rel="icon" href="img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="57x57" href="img/apple-touch-icons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/apple-touch-icons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/apple-touch-icons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icons/apple-touch-icon-152x152.png">-->
    */ ?>
    <?php $this->head() ?>
</head>
<body<?php if(yii::$app->controller->route != 'site/index'): ?> class="inner-page"<?php endif; ?>>
<?php $this->beginBody(); ?>
    <?= $content; ?>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>