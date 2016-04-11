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
use backend\models\Catalogue;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CatalogueSearch */
/* @var $productSearchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $productDataProvider yii\data\ActiveDataProvider */
/* @var $category backend\models\Catalogue */

$parents = $category->parents();

$this->title = $category->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catalogue'), 'url' => ['index']];
for ($i = 0; $i < count($parents) - 1; $i++)
{
    $this->params['breadcrumbs'][] = ['label' => $parents[$i]->name, 'url' => ['category', 'id' => $parents[$i]->id]];
}
$this->params['breadcrumbs'][] = $category->name;


$title = $this->title;
for ($i = count($parents) - 2; $i >= 0; $i--)
{
    $title .= ' :: ' . $parents[$i]->name;
}
$title = $title . ' :: ' . Yii::t('app', 'Catalogue') . ' :: ' . Yii::$app->config->siteName;

?>

<h1><?= Html::encode($this->title) ?></h1>
<h3>Категории</h3>
<?php
$this->title = $title;
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
            <?php /*= Button::widget ( [
                'label' => '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete'),
                'encodeLabel' => false,
                'options' => [
                    'class' => 'btn-danger btn-sm pull-right',
                    'id' => 'category-del-btn',
                    'href' => Url::to(['/catalogue/deletescope']),
                    'style' => 'margin:5px',
                ],
                'tagName' => 'a',
            ] ); */ ?>
            <?php /* $this->registerJs("
                $('#category-del-btn').on('click', function(e){
                    var keys = $('#categoryids').yiiGridView('getSelectedRows');

                    $.ajax({
                        type: 'POST',
                        url: '" . Url::to(['/catalogue/deletescope']) . "',
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
                            $.pjax.reload({container:'#category-gv-container'});
                        },
                        error: function(){
                            bootbox.alert('" . Yii::t('app', 'An error occurred while deleting') . "');
                        }
                    });

                    return false;
                });
            "); */ ?>
            <?= Button::widget ( [
                'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create category'),
                'encodeLabel' => false,
                'options' => [
                    'class' => 'btn-success btn-sm pull-right',
                    'href' => Url::to(['/catalogue/create', 'id' => $parentId]),
                    'style' => 'margin:5px'
                ],
                'tagName' => 'a',
            ] ); ?>
            <div class="clearfix">&nbsp;</div>
            <?php Pjax::begin(['id' => 'category-gv-container']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'categoryids',
                'filterModel' => $searchModel,
                'columns' => [
                    /*[
                        'class' => CheckboxColumn::className(),
                        'checkboxOptions' => [
                            'value' => $model[$key]->id,
                        ],
                        'name' => 'categoryids[]',
                        'contentOptions' => ['style'=>'width: 30px'],
                    ],*/
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function($row){
                            return Html::a($row->name, ['category', 'id' => $row->id], ['data' => ['pjax' => 0]]);
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{update} {delete}',
                        'contentOptions' => ['style'=>'width: 50px'],
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
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
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>Товары</h3>
<?= Button::widget ( [
    'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create product'),
    'encodeLabel' => false,
    'options' => [
        'class' => 'btn-success btn-sm pull-right',
        'href' => Url::to(['/product/create', 'id' => $parentId]),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
] ); ?>
    <div class="clearfix">&nbsp;</div>
<?php Pjax::begin(['id' => 'products-gv-container']); ?>
<?= GridView::widget([
    'dataProvider' => $productDataProvider,
    'id' => 'categoryids',
    'filterModel' => $productSearchModel,
    'columns' => [
        [
            'attribute' => 'name',
        ],
        [
            'class' => ActionColumn::className(),
            'controller' => 'product',
            'template' => '{update} {delete}',
            'contentOptions' => ['style'=>'width: 50px'],
        ],
    ],
]); ?>
<?php Pjax::end(); ?>
