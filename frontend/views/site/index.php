<?php

use frontend\widgets\BlockWidget;
use frontend\widgets\CatalogueWidget;
use frontend\widgets\ArticlesWidget;
use frontend\widgets\LastNewsWidget;

/* @var $this yii\web\View */
?>
<div class="bd-box">
    <div class="container">
        <section class="main-ctg-list-box">
            <h2 class="h1">Выбери лучшее предложение</h2>

                <?= CatalogueWidget::widget(['template' => 'index']); ?>
            </div>
            <div class="clear"></div>
        </section>
    </div>
</div>
<div class="infblock-3-box">
    <div class="container">
        <div class="inf-col inf-col-1">
            <section class="about-inf-block">
                <?= BlockWidget::widget(['position' => 'main_center_left']) ?>
            </section>
        </div>
        <div class="inf-col inf-col-2">
            <?= ArticlesWidget::widget([
                'quantity' => intval(Yii::$app->config->articleItemsPerHome),
            ]); ?>
        </div>
        <div class="inf-col inf-col-3">
            <?= LastNewsWidget::widget([
                'quantity' => intval(Yii::$app->config->newsItemsPerHome),
            ]); ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
