<?php

use yii\bootstrap\Button;
use yii\bootstrap\Alert;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\SlaveProductSearch */
/* @var $products backend\models\Product[] */

$this->title = Yii::t('app', 'Slave products');
$this->params['breadcrumbs'][] = $this->title;

$productsArr = [];
$productImages = [];
$productNames = [];
foreach ($products as $product) {
    $productNames[] = $product->name;
    $productsArr[$product->id] = $product->name;
    $productImages[$product->id] = is_null($product->small_image) ? '/admin/img/no-image.png' : '/uploads/' . $product->id . '/' . $product->small_image;
}
?>
    <h1><?= Html::encode($this->title) ?></h1><?php
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

<?= Button::widget([
    'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create'),
    'encodeLabel' => false,
    'options' => [
        'class' => 'btn-success btn-sm pull-right',
        'href' => Url::to(['/slaveproduct/create']),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
]); ?>
    <div class="clearfix">&nbsp;</div>
<?php Pjax::begin(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        /*[
            'attribute' => 'id',
            'contentOptions' => ['style'=>'width: 50px'],
        ],*/
        [
            'attribute' => 'parent_product_id',
            'label' => 'Родитель. товар',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'parent_product_id',
                'clientOptions' => [
                    'source' => $productNames,
                    'autoFill' => true,
                    'minLength' => 3
                ],
                'options' => [
                    'class' => 'form-control'
                ]
            ]),
            'value' => function ($row) use ($productsArr) {
                return $productsArr[$row->parent_product_id];
            },
//                'contentOptions' => ['style'=>'width: 240px'],
        ],
        /*[
            'attribute' => 'code',
            'contentOptions' => ['style'=>'width: 90px'],
        ],*/
        /*[
            'attribute' => 'name',
        ],*/
        [
            'attribute' => 'size_code',
            'contentOptions' => ['style' => 'width: 150px; text-align: center;'],
        ],
        /*[
            'attribute' => 'enduserprice',
            'label' => 'Цена',
            'contentOptions' => ['style'=>'width: 120px; text-align: right;'],
            'value' => function($row) {
                return Yii::$app->formatter->asDecimal($row->enduserprice, 2);
            }
        ],*/
        // 'weight',
        // 'price',
        // 'price_currency',
        // 'price_name',
        // 'amount',
        // 'free',
        // 'inwayamount',
        // 'inwayfree',

        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => ['style' => 'width: 50px'],
        ],
    ],
]); ?>
<?php Pjax::end(); ?>