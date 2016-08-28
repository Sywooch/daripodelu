<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['updateGiftsDBLogger'],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'moderator',],
            'itemFile' => '@common/components/rbac/items.php',
            'assignmentFile' => '@common/components/rbac/assignments.php',
            'ruleFile' => '@common/components/rbac/rules.php',
        ],
        'backup' => [
            'class' => 'demi\backup\Component',

            // The directory for storing backups files
            'backupsFolder' => dirname(dirname(__DIR__)) . '/downloads/backups', // <project-root>/backups

            // Name template for backup files.
            // if string - return date('Y_m_d-H_i_s')
            //'backupFilename' => 'Y-m-d_His',
            // also can be callable:
            'backupFilename' => function (\demi\backup\Component $component) {
                return 'dump_' . date('Y-m-d_His');
            },

            // Directories that will be added to backup
            'directories' => [],

            // Number of seconds after which the file is considered deprecated and will be deleted.
            // To prevent deleting any files you can set this param as NULL/FALSE/0.
            'expireTime' => 3 * 2592000, // 3 month (1 month = 2592000 seconds)
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
            'schemaCacheDuration' => YII_DEBUG ? 60 : 3600,
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
                    'pattern' => '<controller:\w+>',
                    'route' => '<controller>/index',
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
        'updateGiftsDBLogger' => [
            'class' => 'common\components\UpdateGiftsDBLogger',
        ],
        'yandexMapsApi' => [
            'class' => 'rkdev\yandexmaps\Api',
        ]
    ],
    'language' => 'ru-RU',
];
