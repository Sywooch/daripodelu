<?php

use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\UpdateGiftsDBLog;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UpdateGiftsDBLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'The Log of downloads from Gifts.ru website');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php
$this->title = $this->title . ' :: ' . Yii::$app->config->siteName;

if (Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-danger'
        ],
        'body' => Yii::$app->session->getFlash('error'),
    ]);
}
?>

<?php
if (Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success'
        ],
        'body' => Yii::$app->session->getFlash('success'),
    ]);
}
?>

<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width: 90px'],
            ],
            [
                'attribute' => 'status',
                'filter' => UpdateGiftsDBLog::getStatuses(),
                'value' => function ($model) {
                    return UpdateGiftsDBLog::getStatusName($model->status);
                },
                'contentOptions' => ['style' => 'width: 190px'],
            ],
            [
                'attribute' => 'action',
                'filter' => UpdateGiftsDBLog::getActions(),
                'value' => function ($model) {
                    return UpdateGiftsDBLog::getActionName($model->action);
                },
                'contentOptions' => ['style' => 'width: 190px'],
            ],
            [
                'attribute' => 'item',
                'filter' => UpdateGiftsDBLog::getTypes(),
                'value' => function ($model) {
                    return UpdateGiftsDBLog::getTypeName($model->item);
                },
                'contentOptions' => ['style' => 'width: 190px'],
            ],
//            'item_id',
             'message',
             [
                 'attribute' => 'created_date',
                 'value' => function ($model){
                     return date('d.m.Y H:i:s', strtotime($model->created_date));
                 }
             ],

            /*[
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'width: 50px'],
            ],*/
        ],
        'rowOptions' => function ($model) {
            $options = [];
            if ($model->status == UpdateGiftsDBLog::STATUS_ERROR) {
                $options = ['class' => 'danger',];
            }

            return $options;
        }
    ]); ?>
<?php Pjax::end(); ?>
