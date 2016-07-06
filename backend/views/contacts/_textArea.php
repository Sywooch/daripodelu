<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $contactItems common\models\ContactsItem[] */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'value')->textarea(['style' => 'min-height: 80px; max-width: 600px;', 'maxlength' => 255])->hint(
    Yii::t('app', '<strong>Example:</strong>') . ' г. Санкт-Петербург, Невский пр-кт, 28'
); ?>