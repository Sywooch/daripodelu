<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $files array */

$this->title = Yii::t('app', 'Update user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode(Yii::t('app', 'Update user')) ?></h1>

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
    ]) ?>

</div>
