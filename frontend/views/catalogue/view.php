<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use frontend\widgets\BlockWidget;

/* @var $this yii\web\View */
/* @var $categories frontend\models\Catalogue[] */
/* @var $products frontend\models\Product[] */

$this->params['breadcrumbs'][] = $heading;
?>
<div class="col-2">
    <?php if ( is_array($categories) && count($categories) > 0): ?>
    <div class="ctg-list-box">
        <div class="ctg-list-top"></div>
        <ul class="no-ls ctg-list">
            <?php foreach ($categories as $category): ?>
            <li><a href="<?= Url::to(['catalogue/view', 'uri' => $category->uri]); ?>" title="<?= $category->name; ?>"><?= (mb_strlen($category->name, 'UTF-8') > 22 ? mb_substr($category->name, 0, 22, 'UTF-8') . '...': $category->name) ?></a><span class="items-count"><?= $category->products_count; ?></span></li>
            <?php endforeach; ?>
        </ul>
        <div class="ctg-list-bottom"></div>
    </div>
    <div class="clear-left"></div>
    <?php endif; ?>
    <?= BlockWidget::widget(['template' => 'category', 'position' => 'left']) ?>
</div>
<div class="col-8">
    <main class="main-content">
        <h1><?= $heading; ?></h1>
        <div class="products-list">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="product-item" itemscope itemtype="http://schema.org/Product">
                <div class="panel">
                    <div class="panel-top"></div>
                    <?php if ($product->status_id == 0): ?><span class="marker new">Новинка</span><? endif; ?>
                    <a class="name" href="<?= Url::to(['product/view', 'id' => $product->id]); ?>" itemprop="name">
                        <i class="product-img-border"><img class="product-img" src="<?= $product->smallImageUrl; ?>" alt="" itemprop="image"></i><span><?= Html::encode($product->name); ?></span>
                    </a>
                    <?php
                    list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($product->enduserprice, 2));
                    ?>
                    <div class="offers" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <span class="price"><?= $integerPart; ?><?php if ( ! is_null($fractionalPart)): ?>,<span class="small"><?= $fractionalPart; ?></span><?php endif; ?></span>
                        <span class="price-curr">руб.</span>
                        <meta itemprop="price" content="<?= str_replace('.', ',', yii::$app->formatter->asDecimal($product->enduserprice, 2)); ?>">
                        <meta itemprop="priceCurrency" content="RUB">
                    </div>
                    <dl class="info">
                        <dt>Артикул:</dt>
                        <dd itemprop="productID"><?= Html::encode($product->code); ?></dd>
                        <dt>Бренд:</dt>
                        <dd itemprop="brand"><?php if (is_null($product->brand) || trim($product->brand) == ''): ?>&ndash;<?php else: ?><?= Html::encode($product->brand); ?><?php endif; ?></dd>
                        <dt>На складе:</dt>
                        <dd><?= Html::encode($product->amount); ?> шт.</dd>
                        <dt>Свободно:</dt>
                        <dd><?= Html::encode($product->free); ?> шт.</dd>
                    </dl>
                    <?php if ($product->groupProducts): ?>
                    <?php /* @var $product->groupProducts frontend\models\Product[] */ ?>
                    <div class="similar-products">
                    <?php foreach ($product->groupProducts as $groupProduct): ?>
                        <?= Yii::$app->imageCache->thumb('/uploads/' . $groupProduct->id . '/' . $groupProduct->big_image, '36x36') ?>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </main>
</div>
