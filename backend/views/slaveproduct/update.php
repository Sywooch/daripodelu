<?php

use yii\bootstrap\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SlaveProduct */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = (trim($referrer) == '') ? ['label' => Yii::t('app', 'Slave products'), 'url' => ['index']] : ['label' => Yii::t('app', 'Slave products'), 'url' => $referrer];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="slave-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $this->title = $this->title . ' :: ' . Yii::$app->config->siteName;

    if( Yii::$app->session->hasFlash('error') )
    {
        echo Alert::widget ([
            'options' => [
                'class' => 'alert-danger'
            ],
            'body' => Yii::$app->session->getFlash('error'),
        ]);
    }
    ?>

    <?php
    if( Yii::$app->session->hasFlash('success') )
    {
        echo Alert::widget ([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => Yii::$app->session->getFlash('success'),
        ]);
    }
    ?>

    <?= $this->render('_form', [
        'model' => $model,
        'products' => $products,
        'referrer' => $referrer,
    ]) ?>

</div>
