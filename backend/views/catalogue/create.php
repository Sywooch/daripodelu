<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;


/* @var $this yii\web\View */
/* @var $model backend\models\Catalogue */
/* @var $category backend\models\Catalogue */

$parents = [];
$breadcrumb = [];
if (isset($category) and $category instanceof backend\models\Catalogue)
{
    $parents = $category->parents();
}

$this->title = Yii::t('app', 'Create category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catalogue'), 'url' => ['index']];
for ($i = 0; $i < count($parents) - 1; $i++)
{
    $this->params['breadcrumbs'][] = ['label' => $parents[$i]->name, 'url' => ['category', 'id' => $parents[$i]->id]];
}
if (count($parents) > 0)
{
    $this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['category', 'id' => $category->id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogue-create">

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

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'seoInfo' => $seoInfo,
    ]) ?>

</div>