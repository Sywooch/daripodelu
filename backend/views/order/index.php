<?php

use yii\helpers\Html;
//use yii\grid\GridView;
//use yii\grid\ActionColumn;
use backend\models\Order;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
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

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pjax' => true,
    'columns' => [

        [
            'attribute' => 'id',
            'contentOptions' => ['class' => 'colored', 'style'=>'width: 100px'],
        ],
        [
            'attribute' => 'fio',
            'contentOptions' => ['class' => 'colored'],
        ],
        [
            'attribute' => 'phone',
            'contentOptions' => ['class' => 'colored'],
        ],
        [
            'attribute' => 'email',
            'contentOptions' => ['class' => 'colored'],
        ],
        [
            'attribute' => 'order_date',
            'contentOptions' => ['class' => 'colored'],
            'value' => function($model){
                return date("d.m.Y H:i:s", strtotime($model->order_date));
            },
        ],
        [
            'class'=>'kartik\grid\EditableColumn',
            'attribute' => 'status',
            'editableOptions'=>[
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data' => Order::getStatusOptions(),
                'submitButton' => [
                    'class' => 'btn btn-sm btn-primary',
                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                ],
            ],
            'refreshGrid' => true,
            'filter' => Order::getStatusOptions(),
            'value' => function($model){ return Order::getStatusName($model->status); },
            'contentOptions' => ['style'=>'width: 190px'],
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => ['style'=>'width: 50px'],
        ],
    ],
    'rowOptions' => function ($model) {
        $options = [];
        switch ($model->status)
        {
            case Order::STATUS_NEW :
                $options = ['class' => 'status new',];
                break;
            case Order::STATUS_WAIT :
                $options = ['class' => 'status wait',];
                break;
            case Order::STATUS_PROCESSED :
                $options = ['class' => 'status processed',];
                break;
            case Order::STATUS_CANCELED :
                $options = ['class' => 'status',];
                break;
        }

        return $options;
    }
]); ?>