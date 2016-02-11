<?php

use yii;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="main-ctg-list">
    <?php foreach ($categories as $category): ?>
        <?php /* @var $category frontend\models\Catalogue */ ?>
        <a href="<?= Url::to(['catalogue/view', 'uri' => $category->uri]); ?>">
            <span class="panel"></span>
            <?php if (is_null($category->photo)): ?>
            <span class="no-photo no-photo_158x158">Фотография<br>пока<br>отсутствует...</span>
            <?php else: ?>
            <img src="<?= $category->photo->image_url_158x158; ?>" alt="<?= Html::encode($category->name); ?>">
            <?php endif; ?>
            <span><?= Html::encode($category->name); ?></span>
        </a>
    <?php endforeach; ?>
</div>
