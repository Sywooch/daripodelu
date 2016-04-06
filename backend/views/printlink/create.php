<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PrintLink */

$this->title = Yii::t('app', 'Add print');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prints'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="print-link-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'prints' => $prints,
    ]) ?>

</div>
