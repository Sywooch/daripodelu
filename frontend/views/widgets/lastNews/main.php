<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
?>

<div class="news-block">
    <span class="border top"></span>
    <section>
        <h3><?= Yii::t('app', 'News'); ?></h3>
        <ul class="news-list">
            <?php
            foreach ($newsList as $item):
            /* @var $item \app\models\News */
            ?>
            <li>
                <time datetime="<?= Yii::$app->formatter->asDate($item->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($item->published_date, 'dd MMMM yyyy'); ?></time>
                <?= Html::a($item->name, ['/news/view', 'id' => $item->id]); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <a class="all-news" href="<?= Url::to(['news/index']); ?>"><?= Yii::t('app', 'All news'); ?></a>
    </section>
    <span class="border bottom"></span>
</div>