<?php
return [
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'error' => [
        'type' => 2,
    ],
    'sign-up' => [
        'type' => 2,
    ],
    'index' => [
        'type' => 2,
    ],
    'view' => [
        'type' => 2,
    ],
    'update' => [
        'type' => 2,
    ],
    'delete' => [
        'type' => 2,
    ],
    'article_index' => [
        'type' => 2,
    ],
    'article_view' => [
        'type' => 2,
    ],
    'article_create' => [
        'type' => 2,
    ],
    'article_update' => [
        'type' => 2,
    ],
    'article_delete' => [
        'type' => 2,
    ],
    'block_index' => [
        'type' => 2,
    ],
    'block_view' => [
        'type' => 2,
    ],
    'block_create' => [
        'type' => 2,
    ],
    'block_update' => [
        'type' => 2,
    ],
    'block_delete' => [
        'type' => 2,
    ],
    'block_change_order' => [
        'type' => 2,
    ],
    'catalogue_index' => [
        'type' => 2,
    ],
    'catalogue_view' => [
        'type' => 2,
    ],
    'catalogue_create' => [
        'type' => 2,
    ],
    'catalogue_update' => [
        'type' => 2,
    ],
    'catalogue_delete' => [
        'type' => 2,
    ],
    'contacts_index' => [
        'type' => 2,
    ],
    'contacts_view' => [
        'type' => 2,
    ],
    'contacts_create' => [
        'type' => 2,
    ],
    'contacts_update' => [
        'type' => 2,
    ],
    'contacts_delete' => [
        'type' => 2,
    ],
    'contacts_change_order' => [
        'type' => 2,
    ],
    'image_index' => [
        'type' => 2,
    ],
    'image_view' => [
        'type' => 2,
    ],
    'image_create' => [
        'type' => 2,
    ],
    'image_update' => [
        'type' => 2,
    ],
    'image_delete' => [
        'type' => 2,
    ],
    'image_set_main' => [
        'type' => 2,
    ],
    'image_change_order' => [
        'type' => 2,
    ],
    'menu_index' => [
        'type' => 2,
    ],
    'menu_view' => [
        'type' => 2,
    ],
    'menu_create' => [
        'type' => 2,
    ],
    'menu_update' => [
        'type' => 2,
    ],
    'menu_delete' => [
        'type' => 2,
    ],
    'news_index' => [
        'type' => 2,
    ],
    'news_view' => [
        'type' => 2,
    ],
    'news_create' => [
        'type' => 2,
    ],
    'news_update' => [
        'type' => 2,
    ],
    'news_delete' => [
        'type' => 2,
    ],
    'order_index' => [
        'type' => 2,
    ],
    'order_view' => [
        'type' => 2,
    ],
    'order_update' => [
        'type' => 2,
    ],
    'order_delete' => [
        'type' => 2,
    ],
    'page_index' => [
        'type' => 2,
    ],
    'page_view' => [
        'type' => 2,
    ],
    'page_create' => [
        'type' => 2,
    ],
    'page_update' => [
        'type' => 2,
    ],
    'page_delete' => [
        'type' => 2,
    ],
    'product_index' => [
        'type' => 2,
    ],
    'product_view' => [
        'type' => 2,
    ],
    'product_create' => [
        'type' => 2,
    ],
    'product_update' => [
        'type' => 2,
    ],
    'product_delete' => [
        'type' => 2,
    ],
    'settings_index' => [
        'type' => 2,
    ],
    'settings_view' => [
        'type' => 2,
    ],
    'settings_create' => [
        'type' => 2,
    ],
    'settings_update' => [
        'type' => 2,
    ],
    'settings_delete' => [
        'type' => 2,
    ],
    'user_index' => [
        'type' => 2,
    ],
    'user_view' => [
        'type' => 2,
    ],
    'user_create' => [
        'type' => 2,
    ],
    'user_update' => [
        'type' => 2,
    ],
    'user_delete' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'login',
            'logout',
            'error',
            'sign-up',
            'index',
            'view',
        ],
    ],
    'moderator' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'article_index',
            'article_view',
            'article_create',
            'article_update',
            'article_delete',
            'block_index',
            'block_view',
            'block_create',
            'block_update',
            'block_delete',
            'block_change_order',
            'catalogue_index',
            'catalogue_view',
            'catalogue_create',
            'catalogue_update',
            'catalogue_delete',
            'contacts_index',
            'contacts_view',
            'contacts_create',
            'contacts_update',
            'contacts_delete',
            'contacts_change_order',
            'image_index',
            'image_view',
            'image_create',
            'image_update',
            'image_delete',
            'image_set_main',
            'image_change_order',
            'news_index',
            'news_view',
            'news_create',
            'news_update',
            'news_delete',
            'order_index',
            'order_view',
            'order_update',
            'page_index',
            'page_view',
            'page_create',
            'page_update',
            'page_delete',
            'product_index',
            'product_view',
            'product_create',
            'product_update',
            'product_delete',
            'updateOwnProfile',
            'guest',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'update',
            'delete',
            'menu_index',
            'menu_view',
            'menu_create',
            'menu_update',
            'menu_delete',
            'order_delete',
            'settings_index',
            'settings_view',
            'settings_create',
            'settings_update',
            'settings_delete',
            'user_index',
            'user_view',
            'user_create',
            'user_update',
            'user_delete',
            'moderator',
        ],
    ],
    'updateOwnProfile' => [
        'type' => 2,
        'ruleName' => 'isProfileOwner',
    ],
];
