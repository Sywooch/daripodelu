<?php


namespace common\components\rbac;


class BlockPermissions
{
    const INDEX  = 'block_index';
    const VIEW   = 'block_view';
    const CREATE = 'block_create';
    const UPDATE = 'block_update';
    const DELETE = 'block_delete';
    const CHANGE_ORDER = 'block_change_order';

    private function __construct()
    {
    }
}