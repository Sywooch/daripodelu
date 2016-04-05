<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PrintLink */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Print Link',
]) . $model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Print Links'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->code, 'url' => ['view', 'id' => $model->code]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="print-link-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
