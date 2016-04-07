<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $articles \frontend\models\Article[] */
?>

<section class="articles-inf-block">
    <h2>Полезные статьи</h2>
    <ul class="uf-articles-list">
        <?php foreach ($articles as $item): ?>
        <li>
            <a href="<?= Url::to(['/article/view', 'id' => $item->id]) ?>"><?= Html::encode($item->name); ?><span class="panel"></span></a>
        </li>
        <?php endforeach; ?>
    </ul>
    <a class="more" href="<?= Url::to(['/article/index']) ?>"><span>Еще!</span></a>
</section>