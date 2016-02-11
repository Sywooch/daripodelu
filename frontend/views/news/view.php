<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->params['breadcrumbs'][] = ['label' => $heading, 'url' => ['news/index']];
?>
<main>
    <article>
        <strong class="h1-caps"><?= Html::encode(Yii::t('app', 'News')); ?></strong>
        <hr class="under-h">
        <header>
            <time class="news-date" datetime="<?= Yii::$app->formatter->asDate($model->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($model->published_date, 'dd MMMM yyyy'); ?></time>
            <h1><?= Html::encode($model->name); ?></h1>
        </header>
        <?= $model->content; ?>
    </article>
</main>