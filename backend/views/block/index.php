<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\Button;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\News;
use backend\models\Block;

yii\jui\JuiAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Blocks');
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

<?= Button::widget ( [
    'label' => '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete'),
    'encodeLabel' => false,
    'options' => [
        'class' => 'btn-danger btn-sm pull-right',
        'id' => 'block-del-btn',
        'href' => Url::to(['/block/deletescope']),
        'style' => 'margin:5px',
    ],
    'tagName' => 'a',
] ); ?>
<?php $this->registerJs("
    $('#block-del-btn').on('click', function(e){
        var keys = $('.blockids').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url: '" . Url::to(['/block/deletescope']) . "',
            dataType: 'json',
            data: {ids: keys},
            beforeSend: function(){
                if(keys.length == 0)
                {
                    bootbox.alert('" . Yii::t('app', 'You must select at least one item!') . "');
                    return false;
                }
                if(! confirm('" . Yii::t('app','Are you sure you want to delete selected items?') . "'))
                {
                    return false;
                }

                /*
                bootbox.confirm('" . Yii::t('yii','Are you sure you want to delete this item?') . "', function(result){

                });*/
            },
            success: function(data, textStatus, jqXHR){
                if (data.rslt > 0)
                {
                    bootbox.alert('" . Yii::t('app', 'Selected items deleted successfully!') . "');
                }
                else
                {
                    bootbox.alert('" . Yii::t('app', 'No items was deleted!') . "');
                }
                $.pjax.reload({container:'#blocks-gv-container'});
            },
            error: function(){
                bootbox.alert('" . Yii::t('app', 'An error occurred while deleting') . "');
            }
        });

        return false;
    });
"); ?>
<?= Button::widget ( [
    'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create'),
    'encodeLabel' => false,
    'options' => [
        'class' => 'btn-success btn-sm pull-right',
        'href' => Url::to(['/block/create']),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
] ); ?>
<div class="clearfix">&nbsp;</div>
<?php foreach ($positions as $code => $name): ?>
    <div style="padding: 10px 0 2px; font-size: 24px"><?= $name; ?><?php if ($code != Block::NO_POS): ?> &ndash; {<?= $code ?>}<?php endif; ?></div>
    <?php Pjax::begin(['id' => 'blocks-gv-container-' . $code]); ?>
        <?= Html::a('<span class="glyphicon glyphicon-floppy-save"></span>' . Yii::t('app', 'Save changes'),
            ['order'],
            [
                'class' => 'btn btn-primary btn-sm pull-right',
                'id' => 'save-order_' . $code,
                'disabled' => 'disabled',
                'title' => Yii::t('app', 'Save changes after sorting'),
                'style' => 'margin: 5px;',
            ]
        );
        ?>
        <div class="clearfix">&nbsp;</div>
        <?= GridView::widget([
            'dataProvider' => $dataProviders[$code],
            'filterModel' => null,
            'columns' => [
                [
                    'class' => CheckboxColumn::className(),
                    'checkboxOptions' => [
                        'value' => $model[$key]->id,
                    ],
                    'name' => 'blockids[]',
                    'contentOptions' => ['style'=>'width: 30px'],
                ],
                'name',
                'title',
                // 'show_all_pages',

                [
                    'class' => ActionColumn::className(),
                    'template' => '{update} {delete}',
                    'contentOptions' => ['style'=>'width: 50px'],
                ],
            ],
            'options' => [
                'class' => 'grid-view blockids',
            ],
            'tableOptions' => [
                'id' => 'table_' . $code,
                'class' => 'table table-striped table-bordered',
            ],
        ]); ?>


        <?php $this->registerJs("

            var fixHelper_$code = function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                saveOrder_$code = $('#save-order_$code');

            $('#table_$code tbody').sortable({
                cursor: 'move',
                cursorAt: { left: 12 },
                helper: fixHelper_$code,
                placeholder: 'ui-sortable-placeholder',
                update: function( event, ui ) {
                    saveOrder_$code.attr('disabled', false);
                }
            }).disableSelection();

            saveOrder_$code.on('click', function(e){
                var link = $(this).attr('href'),
                    itemsList = {};

                $('#table_$code tbody tr').each(function(index, value) {
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
                            saveOrder_$code.attr('disabled', true);
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
    <?php Pjax::end(); ?>
    <div style="padding: 10px 0 10px"></div>
<?php endforeach; ?>
