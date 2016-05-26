<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'moderator',],
            'itemFile' => '@common/components/rbac/items.php',
            'assignmentFile' => '@common/components/rbac/assignments.php',
            'ruleFile' => '@common/components/rbac/rules.php',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@frontend/runtime/cache'
        ],
        'config' => [
            'class' => 'common\components\Config',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=site_db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'dpd_',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => YII_DEBUG ? 0 : 3600,
            'schemaCache' => 'cache',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy, HH:mm:ss',
            'decimalSeparator' => '.',
            'locale' => 'ru-RU',
            'timeFormat' => 'HH:mm:ss',
            'thousandSeparator' => ' ',
        ],
        'imageCache' => [
            'class' => 'rkdev\yii2imagecache\ImageCache',
            'sourcePath' => '@app/../uploads',
            'sourceUrl' => '/uploads',
            'sizes' => [
                '36' => [36],
                '90' => [90],
                '36x36' => [36, 36],
                '90x90' => [90, 90],
                '280x200' => [280, 200],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => 'thumbs/<path:.*>',
                    'route' => 'site/thumb',
                    'suffix' => null,
                ],
                [
                    'pattern' => 'cart/delete-size/<productId:[\d]+>_<sizeCode:[A-Za-z0-9_]+>',
                    'route' => 'cart/deletesize',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/<uri:[\w\d\-]+>/filter/<filterParams:[\d]+\.[\d]+(\-[\d]+\.[\d]+)*>/page<page:\d+>',
                    'route' => 'catalogue/view',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/<uri:[\w\d\-]+>/filter/<filterParams:[\d]+\.[\d]+(\-[\d]+\.[\d]+)*>',
                    'route' => 'catalogue/view',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/<uri:[\w\d\-]+>/page<page:\d+>',
                    'route' => 'catalogue/view',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/create/<id:[\d]+>',
                    'route' => 'catalogue/create',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/create',
                    'route' => 'catalogue/create',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'catalogue/<uri:[\w\d\-]+>',
                    'route' => 'catalogue/view',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => 'product/<id:[\d]+>',
                    'route' => 'product/view',
                    'suffix' => '.html',
                ],
                [
                    'class' => 'common\components\CmsUrlRule',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => '<controller:\w+>/<id:\d+>',
                    'route' => '<controller>/view',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => '<controller:\w+>/<action:\w+>/<id:\d+>',
                    'route' => '<controller>/<action>',
                    'suffix' => '.html',
                ],
                [
                    'pattern' => '<controller:\w+>/<action:\w+>',
                    'route' => '<controller>/<action>',
                    'suffix' => '.html',
                ],
            ],
        ],
    ],
    'language' => 'ru-RU',
];
