<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'catalogue_id',
            'group_id',
            'code',
            'name',
            // 'product_size',
            // 'matherial',
            // 'small_image',
            // 'big_image',
            // 'super_big_image',
            // 'content:ntext',
            // 'status_id',
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
            // 'enduserprice',
            // 'user_row',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
