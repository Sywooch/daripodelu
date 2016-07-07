<?php


namespace common\components\rbac;


class OrderPermissions
{
    const INDEX = 'order_index';
    const VIEW = 'order_view';
    const CREATE = 'order_create';
    const UPDATE = 'order_update';
    const DELETE = 'order_delete';

    private function __construct()
    {
    }
}