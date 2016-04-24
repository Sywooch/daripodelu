<?php

use yii\bootstrap\Alert;
use yii\bootstrap\Button;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrintLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Print - Link');
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
        'id' => 'printlink-del-btn',
        'href' => Url::to(['/printlink/deletescope']),
        'style' => 'margin:5px',
    ],
    'tagName' => 'a',
] ); ?>
<?php $this->registerJs("
    $('#printlink-del-btn').on('click', function(e){
        var keys = $('#printlinkids').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url: '" . Url::to(['/printlink/deletescope']) . "',
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
                $.pjax.reload({container:'#printlinks-gv-container'});
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
        'href' => Url::to(['/printlink/create']),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
] ); ?>
<div class="clearfix">&nbsp;</div>
<?php Pjax::begin(['id' => 'printlinks-gv-container']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
                'checkboxOptions' => [
                    'value' => $model[$key]->id,
                ],
                'name' => 'printlinkids[]',
                'contentOptions' => ['style'=>'width: 30px'],
            ],
            [
                'attribute' => 'code',
                'contentOptions' => ['style'=>'width: 150px'],
            ],
            'link',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'contentOptions' => ['style'=>'width: 50px'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>