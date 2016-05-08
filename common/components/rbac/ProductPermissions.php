<?php


namespace common\components\rbac;


class ProductPermissions
{
    const INDEX  = 'product_index';
    const VIEW   = 'product_view';
    const CREATE = 'product_create';
    const UPDATE = 'product_update';
    const DELETE = 'product_delete';

    private function __construct()
    {
    }
}