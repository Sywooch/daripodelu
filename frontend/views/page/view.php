<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\Page */

//$this->params['breadcrumbs'][] = $this->title;
?>
<main>
    <article>
        <h1 class="caps"><?= Html::encode($model->name); ?></h1>
        <hr class="under-h">
        <?= $model->content; ?>
    </article>
</main>