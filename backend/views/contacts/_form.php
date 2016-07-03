<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ContactsItem;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $contactItems common\models\ContactsItem[] */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contacts-item-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройка видимости</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

                <?= $form->field($model, 'type')
                    ->dropDownList(['' => '---'] + ContactsItem::getTypes(), ['style' => 'max-width: 200px;'])
                    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', ''));
                ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>
            </div>
            <div role="tabpanel" id="settings" class="tab-pane">
                <?php
                $contactItemsMap = count($contactItems) > 0 ? ArrayHelper::map($contactItems, 'weight', 'name') : [];
                ?>
                <?= $form->field($model, 'weight')->dropDownList(ArrayHelper::merge(
                    [0 => '--- ' . Yii::t('app', 'At the beginning') . ' ---'],
                    $contactItemsMap
                ), ['style' => 'max-width: 200px;'])->label('Положение после');
                ?>

                <?php
                if ($model->isNewRecord)
                {
                    $model->status = ContactsItem::STATUS_ACTIVE;
                }
                ?>
                <?= $form->field($model, 'status')
                    ->dropDownList(ContactsItem::getStatusOptions(), ['style' => 'max-width: 200px;'])
                    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only contact items with status "Published" is displayed on the site.'));
                ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>

        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveContact']
            ); ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyContact']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
