<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use common\models\ContactsItem;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ContactsItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contacts');
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

<div role="tabpanel">
    <ul class="nav nav-tabs">
        <li role="presentation"<?php if ($tabIndex === 0) : ?> class="active"<? endif; ?>><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
        <li role="presentation"<?php if ($tabIndex === 1) : ?> class="active"<? endif; ?>><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройки</a></li>
    </ul>
    <div class="tab-content cms">
        <div role="tabpanel" id="main" class="tab-pane<?php if ($tabIndex === 0) : ?> active<? endif; ?>">
            <?= Button::widget ( [
                'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create'),
                'encodeLabel' => false,
                'options' => [
                    'class' => 'btn-success btn-sm pull-right',
                    'href' => Url::to(['/contacts/create']),
                    'style' => 'margin:5px'
                ],
                'tagName' => 'a',
            ] ); ?>
            <div class="clearfix">&nbsp;</div>
            <?php Pjax::begin(); ?>    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'contentOptions' => ['style'=>'width: 50px'],
                        ],
                        [
                            'attribute' => 'type',
                            'label' => 'Статус',
                            'filter' => ContactsItem::getTypes(),
                            'contentOptions' => ['style'=>'width: 120px; text-align: center;'],
                            'value' => function($row){
                                return ContactsItem::getTypeName($row->type);
                            },
                        ],
                        'name',
                        'value',
                        'note',
                        [
                            'class'=>'kartik\grid\EditableColumn',
                            'attribute' => 'status',
                            'editableOptions'=>[
                                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                'data' => ContactsItem::getStatusOptions(),
                                'submitButton' => [
                                    'class' => 'btn btn-sm btn-primary',
                                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                                ],
                            ],
                            'refreshGrid' => true,
                            'filter' => ContactsItem::getStatusOptions(),
                            'value' => function($model){ return ContactsItem::getStatusName($model->status); },
                            'contentOptions' => ['style'=>'width: 190px'],
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'template' => '{update} {delete}',
                            'contentOptions' => ['style'=>'width: 50px'],
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
        <div role="tabpanel" id="settings" class="tab-pane<?php if ($tabIndex === 1): ?> active<? endif; ?>">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($seoInfo, 'heading')->textInput(['maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

            <?= $form->field($seoInfo, 'meta_title')->textInput(['maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

            <?= $form->field($seoInfo, 'meta_description')->textarea(['rows' => 3, 'maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

            <?= $form->field($seoInfo, 'meta_keywords')->textarea(['rows' => 3, 'maxlength' => 255, 'style' => 'max-width: 500px;']) ?>

            <div style="padding-bottom: 5px;">&nbsp;</div>
            <div class="form-group btn-ctrl">
                <?= Html::submitButton(
                    Yii::t('app', 'Save'),
                    ['class' => $seoInfo->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveSEO']
                ); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
