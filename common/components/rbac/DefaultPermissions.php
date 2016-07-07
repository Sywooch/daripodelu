<?php


namespace common\components\rbac;


class DefaultPermissions
{
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const ERROR = 'error';
    const SIGNUP = 'sign-up';
    const INDEX = 'index';
    const VIEW = 'view';
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const SAVE_SEO = 'save_seo';

    private function __construct()
    {
    }
}