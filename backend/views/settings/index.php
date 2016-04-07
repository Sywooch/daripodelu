<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\models\SettingsForm;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php
$this->title = $this->title . ' :: ' . Yii::$app->config->siteName;

if( Yii::$app->session->hasFlash('error') )
{
    echo Alert::widget ([
        'options' => [
            'class' => 'alert-danger'
        ],
        'body' => Yii::$app->session->getFlash('error'),
    ]);
}
?>

<?php
if( Yii::$app->session->hasFlash('success') )
{
    echo Alert::widget ([
        'options' => [
            'class' => 'alert-success'
        ],
        'body' => Yii::$app->session->getFlash('success'),
    ]);
}
?>

<div class="settings-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-content cms" style="padding: 20px; border-top: 1px solid #ddd;">
        <?= $form->field($model, 'siteName')->textInput(['maxlength' => 255, 'style' => 'max-width: 800px;']); ?>

        <?= $form->field($model, 'siteAdminEmail')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']); ?>

        <?= $form->field($model, 'siteEmail')
                ->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;'])
                ->hint(
                    Yii::t('app', '<strong>Note:</strong>') . ' ' .
                    Yii::t('app', 'Messages from feedback forms, and reports of new orders are sent to this email address. Also, this email address is used in letters for reply.')); ?>

        <?= $form->field($model, 'sitePhone')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']); ?>

        <?= $form->field($model, 'siteWorkSchedule')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']); ?>

        <?= $form->field($model, 'siteMetaDescription')
                ->textarea(['rows' => 3])
                ->hint(
                    Yii::t('app', '<strong>Note:</strong>') . ' ' .
                    Yii::t('app', 'Used by search engines such as Google, Yandex, etc. in displaying search results.'));
        ?>

        <?= $form->field($model, 'siteMetaKeywords')
                ->textarea()
                ->hint(
                    Yii::t('app', '<strong>Note:</strong>') . ' ' .
                    Yii::t('app', 'Words that best describe the content of the site. It can be both single words and phrases.')); ?>

        <?= $form->field($model, 'newsItemsPerPage')->textInput(['maxlength' => 255, 'style' => 'max-width: 80px;']); ?>

        <?= $form->field($model, 'newsItemsPerHome')->textInput(['maxlength' => 255, 'style' => 'max-width: 80px;']); ?>

        <?= $form->field($model, 'articleItemsPerPage')->textInput(['maxlength' => 255, 'style' => 'max-width: 80px;']); ?>

        <?= $form->field($model, 'articleItemsPerHome')->textInput(['maxlength' => 255, 'style' => 'max-width: 80px;']); ?>

        <?= $form->field($model, 'productsPerPage')->textInput(['maxlength' => 255, 'style' => 'max-width: 80px;']); ?>

        <?= $form->field($model, 'gateLogin')->textInput(['maxlength' => 255, 'style' => 'max-width: 200px;']); ?>

        <?= $form->field($model, 'gatePassword')->textInput(['maxlength' => 255, 'style' => 'max-width: 200px;']); ?>
    </div>
    <div style="padding-bottom: 5px;">&nbsp;</div>
    <div class="form-group btn-ctrl">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            ['class' => 'btn btn-primary', 'name' => 'saveSettings']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>