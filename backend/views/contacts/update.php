<?php

use yii\bootstrap\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $contactItems common\models\ContactsItem[] */

$this->title = Yii::t('app', 'Update Contact');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update Contact');
?>
<div class="contacts-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $this->title = $this->title . ' :: ' . Yii::t('app', 'Contacts') . ' :: ' . Yii::$app->config->siteName;

    if (Yii::$app->session->hasFlash('error')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-danger'
            ],
            'body' => Yii::$app->session->getFlash('error'),
        ]);
    }
    ?>

    <?php
    if (Yii::$app->session->hasFlash('success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => Yii::$app->session->getFlash('success'),
        ]);
    }
    ?>

    <?= $this->render('_form', [
        'model' => $model,
        'contactItems' => $contactItems,
    ]) ?>

</div>