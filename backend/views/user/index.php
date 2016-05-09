<?php

use yii\bootstrap\Button;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\rbac\UserPermissions;
use common\models\User;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
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
<?= Button::widget ( [
    'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Create'),
    'encodeLabel' => false,
    'options' => [
        'class' => 'btn-success btn-sm pull-right',
        'href' => Url::to(['/user/create']),
        'style' => 'margin:5px'
    ],
    'tagName' => 'a',
] ); ?>
    <div class="clearfix">&nbsp;</div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'username',
            [
                'attribute' => 'email',
                'format' => 'email',
                'contentOptions' => ['style'=>'width: 250px'],
            ],
            [
                'attribute' => 'last_name',
//                'contentOptions' => ['style'=>'width: 250px'],
            ],
            [
                'attribute' => 'first_name',
//                'contentOptions' => ['style'=>'width: 250px'],
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'role',
                'editableOptions'=>[
                    'inputType'=> Editable::INPUT_DROPDOWN_LIST,
                    'data' => User::getRoles(),
                    'submitButton' => [
                        'class' => 'btn btn-sm btn-primary',
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                    ],
                ],
                'refreshGrid' => true,
                'filter' => User::getRoles(),
                'value' => function($model){ return $model->roleName; },
                'contentOptions' => ['style'=>'width: 150px'],
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'status',
                'editableOptions'=>[
                    'inputType'=> Editable::INPUT_DROPDOWN_LIST,
                    'data' => User::getStatuses(),
                    'submitButton' => [
                        'class' => 'btn btn-sm btn-primary',
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                    ],
                ],
                'refreshGrid' => true,
                'filter' => User::getStatuses(),
                'value' => function($model){ return $model->statusName; },
                'contentOptions' => ['style'=>'width: 150px'],
            ],
            // 'role',
            // 'created_at',
            // 'updated_at',

            [
                'class' => ActionColumn::className(),
                'template' => Yii::$app->user->can(UserPermissions::DELETE) ? '{update} {delete}' : '{update}',
                'contentOptions' => ['style'=>'width: 50px'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>