<?php

use yii\bootstrap\Button;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\models\Product;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $categories backend\models\Catalogue[] */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;
$categoriesName = [];
$categoriesArr = [];
foreach ($categories as $category)
{
    $categoriesName[] = $category->name;
    $categoriesArr[$category->id] = $category->name;
}

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
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => [
            'class' => 'gv-row-90',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'contentOptions' => ['style'=>'width: 50px'],
            ],
            [
                'format' => 'html',
                'contentOptions' => ['class' => 'gv-td-img-90', 'style'=>'width: 90px'],
                'value' => function($row) {
                    $src = is_null($row->small_image) ? '/admin/img/no-image.png' : '/uploads/' . $row->id . '/' . $row->small_image;

                    return Html::img($src, ['class' => 'gv-prod-img-90']);
                }
            ],
            [
                'attribute' => 'code',
                'contentOptions' => ['style'=>'width: 80px'],
            ],
            [
                'attribute' => 'catalogue_id',
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'catalogue_id',
                    'clientOptions' => [
                        'source' => $categoriesName,
                        'autoFill' => true,
                        'minLength' => 3
                    ],
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'value' => function($row) use ($categoriesArr) {
                    return $categoriesArr[$row->catalogue_id];
                },
                'contentOptions' => ['style'=>'width: 240px'],
            ],
//            'group_id',
            [
                'attribute' => 'name',
            ],
            // 'product_size',
            // 'matherial',
            // 'small_image',
            // 'big_image',
            // 'super_big_image',
            // 'content:ntext',
            [
                'attribute' => 'status_id',
                'label' => 'Статус',
                'filter' => Product::getStatusOptions(),
                'headerOptions' => ['style' => 'white-space: normal; text-align: center;'],
                'contentOptions' => ['style'=>'width: 100px; text-align: center;'],
                'value' => function($row){
                    return Product::getStatusName($row->status_id);
                },
            ],
            // 'status_caption',
            // 'brand',
            // 'weight',
            // 'pack_amount',
            // 'pack_weigh',
            // 'pack_volume',
            // 'pack_sizex',
            // 'pack_sizey',
            // 'pack_sizez',
            // 'amount',
            // 'free',
            // 'inwayamount',
            // 'inwayfree',
            [
                'attribute' => 'enduserprice',
                'contentOptions' => ['style'=>'width: 120px; text-align: right;'],
                'value' => function($row) {
                    return Yii::$app->formatter->asDecimal($row->enduserprice, 2);
                }
            ],
            [
                'attribute' => 'user_row',
                'filter' => Product::getCreateMethods(),
                'headerOptions' => ['style' => 'white-space: normal; text-align: center;'],
                'contentOptions' => ['style'=>'width: 50px; text-align: center;'],
                'value' => function($row){
                    return Product::getCreateMethodName($row->user_row);
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'contentOptions' => ['style'=>'width: 50px'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
