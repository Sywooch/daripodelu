<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\SlaveProduct */
/* @var $form yii\widgets\ActiveForm */
/* @var $products backend\models\Product[] */
?>

<div class="slave-product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

                <?= $form->field($model, 'parent_product_id')->widget(Select2::className(), [
                    'data' => ArrayHelper::map($products, 'id', 'name'),
                    'options' => [
                        'multiple' => false,
                        'placeholder' => 'Выберите родительский товар...',
                    ],
                    'pluginOptions' => [
                        'width' => '600px'
                    ],
                ]); ?>

                <?php//= $form->field($model, 'code')->textInput(['maxlength' => 100, 'style' => 'max-width: 200px;']) ?>

                <?php//= $form->field($model, 'name')->textInput(['maxlength' => 255, 'style' => 'max-width: 600px;']) ?>

                <?= $form->field($model, 'size_code')->textInput(['maxlength' => 255, 'style' => 'max-width: 200px;']) ?>

                <?php//= $form->field($model, 'weight')->textInput(['style' => 'max-width: 200px;'])->label($model->getAttributeLabel('weight') . ', г') ?>

                <?php/*= $form->field($model, 'price')->widget(MaskedInput::className(),[
                    'mask' => '9{1,12}.99',
                    'options' => ['class' => 'form-control', 'style' => 'max-width: 200px;'],
                ])*/ ?>

                <?php//= $form->field($model, 'price_currency')->textInput(['maxlength' => 20, 'style' => 'max-width: 200px;']) ?>

                <?php//= $form->field($model, 'price_name')->textInput(['maxlength' => 40, 'style' => 'max-width: 200px;']) ?>

                <?= $form->field($model, 'amount')->textInput(['style' => 'max-width: 200px;']) ?>

                <?= $form->field($model, 'free')->textInput(['style' => 'max-width: 200px;']) ?>

                <?= $form->field($model, 'inwayamount')->textInput(['style' => 'max-width: 200px;']) ?>

                <?= $form->field($model, 'inwayfree')->textInput(['style' => 'max-width: 200px;']) ?>

                <?php/*= $form->field($model, 'enduserprice')->widget(MaskedInput::className(),[
                    'mask' => '9{1,12}.99',
                    'options' => ['class' => 'form-control', 'style' => 'max-width: 200px;'],
                ])*/ ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveSlave']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applySlave']
            ); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
