<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\Page */
/* @var $pages \common\models\MenuTree[] */

//$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (count($pages) > 0): ?>
<div class="col-2">
    <ul class="no-ls secondary-nav-menu">
    <?php foreach ($pages as $page): ?>
        <li>
            <?php
            $link = [];
            $link = ($page->module_id)? [$page->module_id . '/' . $page->controller_id . '/' . $page->action_id] : [$page->controller_id . '/' . $page->action_id];
            if ($page->item_id)
            {
                $link['id'] = $page->item_id;
            }
            ?>
            <a href="<?= Url::to($link) ?>"><?= Html::encode($page->name); ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<div class="col-8">
<?php else: ?>
<div class="col-10">
<?php endif; ?>
    <main class="main-content">
        <article>
            <h1 class="caps"><?= Html::encode($model->name); ?></h1>
            <?= $model->content; ?>
        </article>
    </main>
</div>