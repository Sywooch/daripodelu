<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<section class="news-inf-block">
    <h2><?= Yii::t('app', 'News'); ?></h2>
    <ul class="news-list-box">
        <?php
        foreach ($newsList as $item):
        /* @var $item \app\models\News */
        ?>
        <li>
            <time datetime="<?= Yii::$app->formatter->asDate($item->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($item->published_date, 'dd MMMM'); ?></time>
            <a href="<?= Url::to(['/news/view', 'id' => $item->id]) ?>"><?= Html::encode($item->name); ?><span class="panel"></span></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>