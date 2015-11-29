<?php

use yii\bootstrap\Button;
use yii\bootstrap\Alert;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\Page;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('app', 'Pages');
$this->params['breadcrumbs'][] = $this->title;


?>
<h1><?= Html::encode($this->title) ?></h1>

<?php
$this->title = $this->title . ' :: ' . Yii::$app->params['SITE.TITLE'];

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
        'id' => 'page-del-btn',
        'href' => Url::to(['/page/deletescope']),
        'style' => 'margin:5px',
    ],
    'tagName' => 'a',
] ); ?>
<?php $this->registerJs("
    $('#page-del-btn').on('click', function(e){
        var keys = $('#pageids').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url: '" . Url::to(['/page/deletescope']) . "',
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
                $.pjax.reload({container:'#pages-gv-container'});
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
        'href' => Url::to(['/page/create']),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
] ); ?>
<div class="clearfix">&nbsp;</div>
<?php Pjax::begin(['id' => 'pages-gv-container']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'pageids',
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => CheckboxColumn::className(), 
            'checkboxOptions' => [
                'value' => $model[$key]->id,
            ],
            'name' => 'pageids[]',
            'contentOptions' => ['style'=>'width: 30px'],
        ],
        'name',
        [
            'attribute' => 'last_update_date',
            'value' => function($model){ 
                return $model->last_update_date ? Yii::$app->formatter->asDatetime($model->last_update_date): '---'; 
            },
            'contentOptions' => ['style'=>'width: 220px'],
        ],
        [
            'attribute' => 'status',
            'filter' => Page::getStatusOptions(),
            'value' => function($model){ return Page::getStatusName($model->status); },
            'contentOptions' => ['style'=>'width: 190px'],
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => ['style'=>'width: 50px'],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>