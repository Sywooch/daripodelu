<?php


namespace frontend\models;

use common\models\ContactsItem;


class Contacts
{
    /**
     * Returns all phone numbers
     *
     * @return \common\models\Contacts[]
     */
    public function getPhones()
    {
        return ContactsItem::find()->where(['type' => ContactsItem::TYPE_PHONE])->all();
    }

    /**
     * Returns all fax numbers
     *
     * @return \common\models\Contacts[]
     */
    public function getFaxes()
    {
        return ContactsItem::find()->where(['type' => ContactsItem::TYPE_FAX])->all();
    }

    /**
     * Returns all email addresses
     *
     * @return \common\models\Contacts[]
     */
    public function getEmails()
    {
        return ContactsItem::find()->where(['type' => ContactsItem::TYPE_EMAIL])->all();
    }

    /**
     * Returns all addresses
     *
     * @return \common\models\Contacts[]
     */
    public function getAddresses()
    {
        return ContactsItem::find()->where(['type' => ContactsItem::TYPE_ADDRESS])->all();
    }

    /**
     * Returns all items of other types (not phone, email, fax, etc.)
     *
     * @return \common\models\Contacts[]
     */
    public function getOther()
    {
        return ContactsItem::find()->where(['type' => ContactsItem::TYPE_OTHER])->all();
    }
}