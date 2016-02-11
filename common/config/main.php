<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
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
            'class' => 'iutbay\yii2imagecache\ImageCache',
            'sourcePath' => '@app/../uploads',
            'sourceUrl' => '/uploads',
//            'thumbsPath' => '@app/web/cache/thumbs',
//            'thumbsUrl' => '@web/cache/thumbs',
            'sizes' => [
                '36x36' => [36, 36],
            //    'medium' => [300, 300],
            //    'large' => [600, 600],
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
                    'pattern' => 'catalogue/<uri:[\w\d\-]+>',
                    'route' => 'catalogue/view',
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