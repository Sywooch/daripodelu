<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\Catalogue */

$parents = [];
$parents = $model->parents();

$this->title = Yii::t('app', 'Update category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catalogue'), 'url' => ['index']];
for ($i = 0; $i < count($parents) - 1; $i++)
{
    $this->params['breadcrumbs'][] = ['label' => $parents[$i]->name, 'url' => ['category', 'id' => $parents[$i]->id]];
}
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="catalogue-update">

    <h1><?= Html::encode(Yii::t('app', 'Update category')) ?></h1>

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

    <?= $this->render('_form', [
        'model' => $model,
        'seoInfo' => $seoInfo,
        'categories' => $categories,
        'tabIndex' => $tabIndex,
    ]) ?>

</div>