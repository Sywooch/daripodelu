<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'catalogue_id')->textInput() ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'matherial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'small_image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'big_image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'super_big_image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status_id')->textInput() ?>

    <?= $form->field($model, 'status_caption')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'pack_amount')->textInput() ?>

    <?= $form->field($model, 'pack_weigh')->textInput() ?>

    <?= $form->field($model, 'pack_volume')->textInput() ?>

    <?= $form->field($model, 'pack_sizex')->textInput() ?>

    <?= $form->field($model, 'pack_sizey')->textInput() ?>

    <?= $form->field($model, 'pack_sizez')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'free')->textInput() ?>

    <?= $form->field($model, 'inwayamount')->textInput() ?>

    <?= $form->field($model, 'inwayfree')->textInput() ?>

    <?= $form->field($model, 'enduserprice')->textInput() ?>

    <?= $form->field($model, 'user_row')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
