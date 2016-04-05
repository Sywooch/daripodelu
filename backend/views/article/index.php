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
use backend\models\Article;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Articles');
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
    

<div role="tabpanel">
    <ul class="nav nav-tabs">
        <li role="presentation"<?php if ($tabIndex === 0) : ?> class="active"<? endif; ?>><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
        <li role="presentation"<?php if ($tabIndex === 1) : ?> class="active"<? endif; ?>><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройки</a></li>
    </ul>
    <div class="tab-content cms">
        <div role="tabpanel" id="main" class="tab-pane<?php if ($tabIndex === 0) : ?> active<? endif; ?>">
            <?= Button::widget ( [
                'label' => '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete'),
                'encodeLabel' => false,
                'options' => [
                    'class' => 'btn-danger btn-sm pull-right',
                    'id' => 'article-del-btn',
                    'href' => Url::to(['/article/deletescope']),
                    'style' => 'margin:5px',
                ],
                'tagName' => 'a',
            ] ); ?>
            <?php $this->registerJs("
                $('#article-del-btn').on('click', function(e){
                    var keys = $('#articleids').yiiGridView('getSelectedRows');

                    $.ajax({
                        type: 'POST',
                        url: '" . Url::to(['/article/deletescope']) . "',
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
                            $.pjax.reload({container:'#article-gv-container'});
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
                    'href' => Url::to(['/article/create']),
                    'style' => 'margin:5px'
                ],
                'tagName' => 'a',
            ] ); ?>
            <div class="clearfix">&nbsp;</div>
            <?php Pjax::begin(['id' => 'article-gv-container']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'articleids',
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => CheckboxColumn::className(),
                        'checkboxOptions' => [
                            'value' => $model[$key]->id,
                        ],
                        'name' => 'articleids[]',
                        'contentOptions' => ['style'=>'width: 30px'],
                    ],
                    'name',
                    [
                        'attribute' => 'published_date',
                        'format' => 'datetime',
                        'contentOptions' => ['style'=>'width: 220px'],
                    ],
                    [
                        'attribute' => 'last_update_date',
                        'value' => function($model){
                            return $model->last_update_date ? Yii::$app->formatter->asDatetime($model->last_update_date): '---';
                        },
                        'contentOptions' => ['style'=>'width: 220px'],
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Article::getStatusOptions(),
                        'value' => function($model){ return Article::getStatusName($model->status); },
                        'contentOptions' => ['style'=>'width: 190px'],
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