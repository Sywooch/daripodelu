<?php

namespace frontend\models;

use common\models\ContactsItem as CommonContactsItem;

/**
 * Class ContactsItem
 *
 * @package frontend\models
 * @inheritdoc
 */
class ContactsItem extends CommonContactsItem
{
    /**
     * Returns the list of all contact's items with active status
     *
     * @return array|ContactsItem[]
     */
    public function getItems()
    {
        return $this->find()->where(['status' => ContactsItem::STATUS_ACTIVE])->orderBy(['weight' => SORT_ASC])->all();
    }
}