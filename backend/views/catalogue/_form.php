<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Catalogue */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array */
?>

<div class="catalogue-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#photo" aria-controls="photo" role="tab" data-toggle="tab">Фото</a></li>
<!--            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройки</a></li>-->
            <li role="presentation"><a href="#seo" aria-controls="seo" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'uri')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'parent_id')->widget(Select2::className(), [
                    'data' => $categories,
                    'options' => ['placeholder' => 'Выберите родительскую категорию ...'],
                ]) ?>
            </div>
            <div role="tabpanel" id="photo" class="tab-pane">
                <?php if ( ! $model->isNewRecord): ?>
                    <?= $this->render('@app/views/image/_gridview', [
                        'dataProvider' => $model->behaviors['photo']->dataProvider,
                    ]) ?>
                <?php else: ?>

                    <?= Yii::t('app', 'Images can be added only after the category will be saved.') ?>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                <?php endif; ?>

            </div>
            <div role="tabpanel" id="settings" class="tab-pane">
            </div>
            <div role="tabpanel" id="seo" class="tab-pane">
                <?= $form->field($seoInfo, 'heading')->textInput(['maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

                <?= $form->field($seoInfo, 'meta_title')->textInput(['maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

                <?= $form->field($seoInfo, 'meta_description')->textarea(['rows' => 3, 'maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

                <?= $form->field($seoInfo, 'meta_keywords')->textarea(['rows' => 3, 'maxlength' => 255, 'style' => 'max-width: 500px;']) ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveCategory']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyCategory']
            ); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
