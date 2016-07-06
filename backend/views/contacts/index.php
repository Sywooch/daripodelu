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

yii\jui\JuiAsset::register($this);

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
            <?php //Pjax::begin(); ?>
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
            <?= Html::a('<span class="glyphicon glyphicon-floppy-save"></span>' . Yii::t('app', 'Save changes'),
                ['change-order'],
                [
                    'class' => 'btn btn-primary btn-sm pull-right',
                    'id' => 'save-order',
                    'disabled' => 'disabled',
                    'title' => Yii::t('app', 'Save changes after sorting'),
                    'style' => 'margin: 5px;',
                ]
            ); ?>
            <div class="clearfix">&nbsp;</div>
            <?= GridView::widget([
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout'=>true,
                        'options'=>['id'=>'contactsGridView'],
                    ],
                    'options' => [
                        'id' => 'contactsGridView'
                    ],
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'format' => 'html',
                            'contentOptions' => ['style'=>'width: 10px;'],
                            'value' => function($row){
                                return '<span class="glyphicon glyphicon-option-vertical drag-balloon"></span> ';
                            },
                        ],
                        [
                            'attribute' => 'type',
                            'filter' => ContactsItem::getTypes(),
                            'contentOptions' => ['style'=>'width: 120px; text-align: center;'],
                            'value' => function($row){
                                return ContactsItem::getTypeName($row->type);
                            },
                        ],
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'name',
                            'editableOptions' => [
                                'size' => 'lg',
                                'submitButton' => [
                                    'class' => 'btn btn-sm btn-primary',
                                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                                ],
                                'ajaxSettings' => [
                                    'type' => 'post',
                                    'url' => Url::to(['update-name', 'id' => $model->id]),
                                ],
                            ],
                            'refreshGrid' => true,
                        ],
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'value',
                            'editableOptions' => [
                                'size' => 'lg',
                                'submitButton' => [
                                    'class' => 'btn btn-sm btn-primary',
                                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                                ],
                                'ajaxSettings' => [
                                    'type' => 'post',
                                    'url' => Url::to(['update-value', 'id' => $model->id]),
                                ],
                            ],
                            'refreshGrid' => true,
                        ],
//                        'note',
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
            <?php //Pjax::end(); ?>
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

<?php $this->registerJs("

    var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        },
        saveOrder = $('#save-order');

    $('.table tbody').sortable({
        cursor: 'move',
        cursorAt: { left: 12 },
        helper: fixHelper,
        placeholder: 'ui-sortable-placeholder',
        update: function( event, ui ) {
            saveOrder.attr('disabled', false);
        }
    }).disableSelection();

    $(document).on('pjax:complete', function() {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        },
        saveOrder = $('#save-order');

        $('.table tbody').sortable({
            cursor: 'move',
            cursorAt: { left: 12 },
            helper: fixHelper,
            placeholder: 'ui-sortable-placeholder',
            update: function( event, ui ) {
                saveOrder.attr('disabled', false);
            }
        }).disableSelection();
    });

    saveOrder.on('click', function(e){
        var link = $(this).attr('href'),
            itemsList = {};

        $('.table tbody tr').each(function(index, value) {
            itemsList[index] = $(this).attr('data-key');
        });

        $.ajax({
            type: 'POST',
            url: link,
            dataType: 'json',
            data: {sortData: itemsList},
            beforeSend: function(){},
            success: function(data, textStatus, jqXHR){
                if (data.status == 'success')
                {
                    bootbox.alert('" . Yii::t('app', 'The changes was successfully saved!') . "');
                    saveOrder.attr('disabled', true);
                }
                else if (data.status == 'no_updated')
                {
                    bootbox.alert('" . Yii::t('app', 'No changes was made!') . "');
                }
                else
                {
                    bootbox.alert('" . Yii::t('app', 'No changes was made!') . "');
                }
            },
            error: function(){
                bootbox.alert('" . Yii::t('app', 'An error occurred while updating!') . "');
            },
            statusCode: {
                404: function() {
                  bootbox.alert('" . Yii::t('app', 'Page not found!') . "');
                },
                500: function() {
                  bootbox.alert('" . Yii::t('app', 'Internal server error!') . "');
                },
            }
        });

        return false;
    });
"); ?>
