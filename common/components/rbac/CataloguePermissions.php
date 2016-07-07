<?php


namespace common\components\rbac;


class CataloguePermissions
{
    const INDEX = 'catalogue_index';
    const VIEW = 'catalogue_view';
    const CREATE = 'catalogue_create';
    const UPDATE = 'catalogue_update';
    const DELETE = 'catalogue_delete';

    private function __construct()
    {
    }
}