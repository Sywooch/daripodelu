<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->params['breadcrumbs'][] = $heading;
?>
<section>
    <h1 class="caps"><?= $heading; ?></h1>
    <hr class="under-h">
    <?php if (count($news) > 0): ?>
    <ul class="news-list">
    <?php foreach ($news as $model):
        /* @var $model app\models\News */
    ?>
        <li>
            <time datetime="<?= Yii::$app->formatter->asDate($model->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($model->published_date, 'dd MMMM yyyy'); ?></time>
            <a class="title" href="<?= Url::to(['news/view', 'id' => $model->id]) ?>"><?= Html::encode($model->name); ?></a>
            <div class="intro"><?= ! empty($model->intro)? $model->intro: StringHelper::truncateWords(strip_tags($model->content), 20); ?></div>
            <a class="more" href="<?= Url::to(['news/view', 'id' => $model->id]) ?>"><?= Yii::t('app', 'Read more') ?> <span class="icon icon-arrow-gr"></span></a>
            <div class="clear-right"></div>
        </li>
    <?php endforeach; ?>
    </ul>
    <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination inl-blck'],
    ]); ?>
    <?php else: ?>
        <p>Нет новостей.</p>
    <?php endif; ?>
</section>
