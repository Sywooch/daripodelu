<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\News */
/* @var $lastNews frontend\models\News[] */

$this->params['breadcrumbs'][] = ['label' => $heading, 'url' => ['news/index']];
?>
<div class="col-10">
    <main class="main-content">
        <article class="article">
            <header>
                <time class="article-date" datetime="<?= Yii::$app->formatter->asDate($model->published_date, 'yyyy-MM-dd'); ?>"><?= Yii::$app->formatter->asDate($model->published_date, 'dd.MM.yyyy'); ?></time>
                <h1><?= Html::encode($model->name); ?></h1>
            </header>
            <?= $model->content; ?>
        </article>
    </main>
</div>
<?php $this->beginBlock('article'); ?>
<div class="article-line" style="margin-bottom: 50px;"></div>
<div class="container">
    <div class="row">
        <section class="articles-list">
            <?php foreach ($lastNews as $model): ?>
                <section class="articles-item">
                    <a href="<?= Url::to(['news/view', 'id' => $model->id]) ?>">
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
    </div>
</div>
<?php $this->endBlock(); ?>