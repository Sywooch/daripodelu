<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'value')->textInput(['type' => 'email', 'style' => 'max-width: 600px;', 'maxlength' => 255])->hint(
    Yii::t('app', '<strong>Example:</strong>') . ' info@daripodelu.ru'
); ?>

<?= $form->field($model, 'note')
    ->textInput(['style' => 'max-width: 600px;', 'maxlength' => true])
    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', '')); ?>