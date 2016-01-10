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
            'rules' => [
                ['class' => 'common\components\CmsUrlRule'],
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
            'showScriptName' => false,
            'suffix' => '.html',
        ],
    ],
    'language' => 'ru-RU',
];