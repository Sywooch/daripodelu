<?php
return [
    'adminEmail' => 'ratmir85@gmail.com',
    'baseUploadURL' => '/uploads',
    'cacheNavExpire' => 4 * 3600,  //The number of seconds in which the cache of navigation menu will expire. 0 means never expire.
    'gate' => [
        'product' => 'http://ratmir3025.tmweb.ru/product.xml',
        'tree' => 'http://ratmir3025.tmweb.ru/tree.xml',
        'stock' => 'http://ratmir3025.tmweb.ru/stock.xml',
        'filters' => 'http://ratmir3025.tmweb.ru/filters.xml',
    ],
    /*'gate' => [
        'product' => 'http://api2.gifts.ru/export/v2/catalogue/product.xml',
        'tree' => 'http://api2.gifts.ru/export/v2/catalogue/tree.xml',
        'stock' => 'http://api2.gifts.ru/export/v2/catalogue/stock.xml',
        'filters' => 'http://api2.gifts.ru/export/v2/catalogue/filters.xml',
    ],*/
    'imageMaxWidth' => 1920,
    'imageMaxHeight' => 1080,
    'maxImgFileSize' => 2048000,
    'supportEmail' => 'support@example.com',
    'uploadPath' => dirname(__DIR__) . '/../uploads',
    'xmlUploadPath' => [
        'current' => dirname(__DIR__) . '/../downloads/current',
        'archive' => dirname(__DIR__) . '/../downloads/archive',
    ],
    'user.passwordResetTokenExpire' => 3600,
];
