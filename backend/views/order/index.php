<?php

use yii\bootstrap\Alert;
use yii\helpers\Html;
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
<div class="alert alert-info" role="alert">Только заказ со статусом <strong>"<?= Order::getStatusName(Order::STATUS_CANCELED); ?>"</strong> или <strong>"<?= Order::getStatusName(Order::STATUS_ARCHIVE); ?>"</strong> можно удалить.</div>
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
            'contentOptions' => ['style'=>'width: 50px'],
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url) {
                    $options = [
                        'title' => Yii::t('kvgrid', 'Update'),
                        'data-pjax' => '0'
                    ];

                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },
                'delete' => function ($url, $model) {
                    $options = [
                        'title' => Yii::t('kvgrid', 'Delete'),
                        'data-confirm' => Yii::t('kvgrid', 'Are you sure to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0'
                    ];

                    return ($model->status == Order::STATUS_ARCHIVE || $model->status == Order::STATUS_CANCELED) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options) : false;
                },
            ],
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
            case Order::STATUS_ARCHIVE :
                $options = ['class' => 'status archive',];
                break;
            case Order::STATUS_CANCELED :
                $options = ['class' => 'status',];
                break;
        }

        return $options;
    }
]); ?>