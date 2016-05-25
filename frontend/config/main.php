<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['cart', 'log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'assetManager' => [],
        'cart' => [
            'class' => 'frontend\components\cart\ShopCart',
            'validityPeriod' => $params['cartCookieValidityPeriod'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request'=>[
            'class' => 'common\components\Request',
            'web'=> '/frontend/web'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'view' => [
            'class' => '\smilemd\htmlcompress\View',
            'compress' => YII_ENV_DEV ? false : true,
        ],
    ],
    'params' => $params,
];
