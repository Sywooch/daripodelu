<?php

use common\models\MenuTree;
use yii\bootstrap\Alert;
use yii\grid\ActionColumn;
use backend\widgets\grid\TreeGrid;
use yii\helpers\Html;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $searchModel app\models\MenuTreeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Menu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

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

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </p>

    <div class="clearfix">&nbsp;</div>

    <?php
    $dataProvider->pagination = false;
    echo TreeGrid::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($item) {
                    return Html::tag('span', StringHelper::truncate($item->name, 100), ['title' => $item->name]);
                },
            ],
            'alias',
            [
                'attribute' => 'show_in_menu',
                'format' => 'raw',
                'filter' => MenuTree::getShowMenuStatuses(),
                'value' => function ($item) {
                    return ($item->show_in_menu === MenuTree::SHOW_IN_MENU) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                },
                'contentOptions' => ['style' => 'width: 190px; text-align: center;'],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => MenuTree::getStatusOptions(),
                'value' => function ($item) {
                    return ($item->status === MenuTree::STATUS_ACTIVE) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                },
                'contentOptions' => ['style' => 'width: 190px; text-align: center;'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return ($model->id != 1) ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        return ($model->id != 1) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]) : '';
                    },
                ],
                'contentOptions' => ['style' => 'width: 50px'],
            ],
        ],
        'tableOptions' => [
            'id' => 'menu-tree-grid',
            'class' => 'table table-striped table-bordered tree',
        ],
    ]); ?>

</div>
