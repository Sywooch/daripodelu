<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\Page */

//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-10">
    <main class="main-content">
        <article>
            <h1 class="caps"><?= Html::encode($model->name); ?></h1>
            <?= $model->content; ?>
        </article>
    </main>
</div>