<?php


namespace common\components\rbac;


class UserPermissions
{
    const INDEX = 'user_index';
    const VIEW = 'user_view';
    const CREATE = 'user_create';
    const UPDATE = 'user_update';
    const DELETE = 'user_delete';
    const UPDATE_OWN_PROFILE = 'updateOwnProfile';

    private function __construct()
    {
    }
}