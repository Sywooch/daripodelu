<?php
use backend\assets\AppAsset;
use app\assets\BootboxAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\models\Order;
use common\components\rbac\LogPermissions;
use common\components\rbac\MenuPermissions;
use common\components\rbac\SettingsPermissions;
use common\components\rbac\UserPermissions;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php

    NavBar::begin([
        'brandLabel' => 'Панель администратора',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $newOrdersCount = Order::getNewOrdersCount();
    $newOrdersCountString = ($newOrdersCount > 0) ? ' +' . $newOrdersCount : '';

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left', 'style' => 'margin-left: 40px;'],
        'items' => [
            [
                'label' => 'Заказы' . $newOrdersCountString, 'url' => ['/order/index']
            ],
            [
                'label' => 'Каталог',
                'items' => [
                    ['label' => 'Категории', 'url' => ['/catalogue/index']],
                    ['label' => 'Все товары', 'url' => ['/product/index']],
                    ['label' => 'Все дочерние товары', 'url' => ['/slaveproduct/index']],
                    ['label' => '"Методы нанесения - Страницы описания"', 'url' => ['/printlink/index']],
                ],
            ],
            ['label' => 'Меню', 'url' => ['/menutree/index'], 'visible' => Yii::$app->user->can(MenuPermissions::INDEX)],
            [
                'label' => 'Контент',
                'items' => [
                    ['label' => 'Статичные страницы', 'url' => ['/page/index']],
                    ['label' => 'Новости', 'url' => ['/news/index']],
                    ['label' => 'Статьи', 'url' => ['/article/index']],
                    ['label' => 'Блоки', 'url' => ['/block/index']],
                    ['label' => 'Контакты', 'url' => ['/contacts/index']],
                ]
            ],
            [
                'label' => 'Система',
                'items' => [
                    ['label' => 'Журнал работы с Gifts.ru', 'url' => ['/update-gifts-log/index'], 'visible' => Yii::$app->user->can(LogPermissions::INDEX)],
                    ['label' => 'Настройки', 'url' => ['/settings/index'], 'visible' => Yii::$app->user->can(SettingsPermissions::INDEX)],
                ],
            ],
            ['label' => 'Пользователи', 'url' => ['/user/index'], 'visible' => Yii::$app->user->can(UserPermissions::INDEX)],
        ]
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => Yii::$app->user->identity->username,
                'items' => [
                    ['label' => 'Профиль', 'url' => ['/user/update', 'id' => Yii::$app->user->id]],
                    ['label' => 'Выйти', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                ]
            ]
        ]
    ]);
    NavBar::end();
    ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => [
                'label' => 'Главная',
                'url' => ['site/index'],
            ],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= date('Y') ?> <?= Yii::$app->config->siteName; ?></p>

        <p class="pull-right"><? /*= Yii::powered()*/ ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
