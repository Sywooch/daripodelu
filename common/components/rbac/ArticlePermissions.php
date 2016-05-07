<?php


namespace common\components\rbac;


class ArticlePermissions
{
    const INDEX  = 'article_index';
    const VIEW   = 'article_view';
    const CREATE = 'article_create';
    const UPDATE = 'article_update';
    const DELETE = 'article_delete';

    private function __construct()
    {
    }
}