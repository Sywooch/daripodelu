<?php


namespace common\components\rbac;


class ContactsPermissions
{
    const INDEX = 'contacts_index';
    const VIEW = 'contacts_view';
    const CREATE = 'contacts_create';
    const UPDATE = 'contacts_update';
    const DELETE = 'contacts_delete';
    const CHANGE_ORDER = 'contacts_change_order';

    private function __construct()
    {
    }
}