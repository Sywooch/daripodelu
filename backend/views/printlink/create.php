<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PrintLink */

$this->title = Yii::t('app', 'Create Print Link');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Print Links'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="print-link-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
