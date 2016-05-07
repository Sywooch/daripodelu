<?php


namespace common\components\rbac;


class NewsPermissions
{
    const INDEX  = 'news_index';
    const VIEW   = 'news_view';
    const CREATE = 'news_create';
    const UPDATE = 'news_update';
    const DELETE = 'news_delete';

    private function __construct()
    {
    }
}