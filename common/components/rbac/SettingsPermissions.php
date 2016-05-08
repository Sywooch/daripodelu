<?php


namespace common\components\rbac;


class SettingsPermissions
{
    const INDEX  = 'settings_index';
    const VIEW   = 'settings_view';
    const CREATE = 'settings_create';
    const UPDATE = 'settings_update';
    const DELETE = 'settings_delete';

    private function __construct()
    {
    }
}