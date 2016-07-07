<?php


namespace common\components\rbac;


class ImagePermissions
{
    const INDEX = 'image_index';
    const VIEW = 'image_view';
    const CREATE = 'image_create';
    const UPDATE = 'image_update';
    const DELETE = 'image_delete';
    const SET_MAIN = 'image_set_main';
    const CHANGE_ORDER = 'image_change_order';

    private function __construct()
    {
    }
}