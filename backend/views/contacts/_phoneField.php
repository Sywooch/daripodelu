<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'value')->textInput(['type' => 'tel', 'style' => 'max-width: 600px;', 'maxlength' => 255])->hint(
    Yii::t('app', '<strong>Example:</strong>') . ' +7 (988) 413-11-22'
); ?>

<?= $form->field($model, 'note')
    ->textInput(['style' => 'max-width: 600px;', 'maxlength' => true])
    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', '')); ?>