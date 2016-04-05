<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $articles frontend\models\Article[] */

$this->params['breadcrumbs'][] = $heading;
?>
<div class="col-10">
    <main class="main-content">
        <h1><?= $heading; ?></h1>
        <?php if (count($articles) > 0): ?>
        <section class="articles-list">
            <?php foreach ($articles as $model): ?>
            <section class="articles-item">
                <a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>">
                    <?php if ($model->mainPhoto): ?>
                        <?= Yii::$app->imageCache->thumb($model->mainPhoto->getUrl(), '280x200') ?>
                    <?php else: ?>
                        <span class="no-photo no-photo_280x200">Фотография<br>пока<br>отсутствует...</span>
                    <?php endif; ?>
                    <span class="title"><span class="underscore"><?= Html::encode($model->name); ?></span></span>
                    <time datetime="<?= Yii::$app->formatter->asDate($model->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($model->published_date, 'dd.MM.yyyy'); ?></time>
                </a>
            </section>
            <?php endforeach; ?>
        </section>
        <?= LinkPager::widget([
            'pagination' => $pages,
            'options' => ['class' => 'pagination inl-blck'],
        ]); ?>
        <?php else: ?>
            <p>Нет новостей.</p>
        <?php endif; ?>
    </main>
</div>