<?php


namespace common\components\rbac;


class PagePermissions
{
    const INDEX = 'page_index';
    const VIEW = 'page_view';
    const CREATE = 'page_create';
    const UPDATE = 'page_update';
    const DELETE = 'page_delete';

    private function __construct()
    {
    }
}