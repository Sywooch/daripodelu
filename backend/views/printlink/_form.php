<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model backend\models\PrintLink */
/* @var $prints backend\models\PrintKind[] */
?>

<div class="page-form">
    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

            <?php
            $printsArr = [];
            foreach ($prints as $print)
            {
                $printsArr[$print->name] = $print->name . ' - ' . $print->description;
            }
            ?>
            <?= $form->field($model, 'code')->dropDownList(array_merge(['' => 'Выберите метод нанесения...'], $printsArr)); ?>

            <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
            </div>

        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'savePrintLink']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyPrintLink']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

</div>
