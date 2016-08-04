<?php


namespace common\components\rbac;


class LogPermissions
{
    const INDEX = 'log_index';
    const VIEW = 'log_view';
    const CREATE = 'log_create';
    const UPDATE = 'log_update';
    const DELETE = 'log_delete';

    private function __construct()
    {
    }
}