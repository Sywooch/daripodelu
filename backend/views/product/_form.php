<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Product;
use kartik\select2\Select2;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

/* @var $this yii\web\View */
/* @var $model backend\models\Product */
/* @var $form yii\widgets\ActiveForm */
/* @var $prints backend\models\PrintKind[] */

$printsArr =  ArrayHelper::map($prints, 'name', 'description');
foreach ($printsArr as $key => &$value)
{
    $value = $key . ' - ' . $value;
}
?>
<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#pack" aria-controls="pack" role="tab" data-toggle="tab">Упаковка</a></li>
            <li role="presentation"><a href="#photo" aria-controls="photo" role="tab" data-toggle="tab">Доп. фотографии</a></li>
            <li role="presentation"><a href="#files" aria-controls="files" role="tab" data-toggle="tab">Файлы</a></li>
            <li role="presentation"><a href="#filters" aria-controls="filters" role="tab" data-toggle="tab">Применяемые фильтры</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'style' => 'max-width: 700px;',]) ?>

                <?= $form->field($model, 'product_size')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'matherial')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'prints')->widget(Select2::className(), [
                    'data' => $printsArr,
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'width' => '700px'
                    ],
                ]); ?>

                <?= $form->field($model, 'content')->widget(CKEditor::className(),[
                    'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinder',
                        [
                            'preset' => 'full',
                            'inline' => false,
                        ]
                    ),
                ]); ?>

                <?= $form->field($model, 'status_id')->dropDownList(Product::getStatusOptions(), ['style' => 'max-width: 400px;',])->label('Статус'); ?>

                <?= $form->field($model, 'brand')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'weight')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'free')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'inwayamount')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'inwayfree')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'enduserprice')->textInput(['style' => 'max-width: 200px;',]) ?>

            </div>
            <div role="tabpanel" id="pack" class="tab-pane">
                <?= $form->field($model, 'pack_amount')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_weigh')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_volume')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizex')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizey')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizez')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'amount')->textInput(['style' => 'max-width: 200px;',]) ?>
            </div>
            <div role="tabpanel" id="photo" class="tab-pane">
            </div>
            <div role="tabpanel" id="files" class="tab-pane">
            </div>
            <div role="tabpanel" id="filters" class="tab-pane">
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
