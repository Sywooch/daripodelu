<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%contacts_item}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property string $value
 * @property integer $weight
 * @property integer $status
 */
class ContactsItem extends \yii\db\ActiveRecord
{
    const TYPE_PHONE = 'phone';
    const TYPE_FAX = 'fax';
    const TYPE_EMAIL = 'email';
    const TYPE_ADDRESS = 'address';
    const TYPE_OTHER = 'other';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const SCENARIO_INSERT_PHONE = 'insert_phone';
    const SCENARIO_INSERT_EMAIL = 'insert_email';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contacts_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'value', 'weight'], 'required'],
            [['weight', 'status'], 'integer'],
            [['type'], 'string', 'max' => 40],
            [['name', 'value', 'note'], 'string', 'max' => 255],
            [
                ['value'],
                'email',
                'when' => function ($model) {
                    return $model->type == ContactsItem::TYPE_EMAIL;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#contactTypeFld').val() == '" . ContactsItem::TYPE_EMAIL . "';
                }",
            ],
            [
                ['value'],
                'match',
                'pattern' => '/^((8|\+7|\+[0-9]{1,3})[\- ]?)?(\(?\d{2,5}\)?[\- ]?)?[\d\- ]{6,10}$/',
                'message' => Yii::t('app', '{attribute} is not a valid phone number.', ['attribute' => $this->getAttributeLabel('value')]),
                'when' => function ($model) {
                    return $model->type == ContactsItem::TYPE_PHONE || $model->type == ContactsItem::TYPE_FAX;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#contactTypeFld').val() == '" . ContactsItem::TYPE_PHONE . "' || $('#contactTypeFld').val() == '" . ContactsItem::TYPE_FAX . "';
                }",
            ],
            [['name', 'value', 'note',], 'trim', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Тип'),
            'name' => Yii::t('app', 'Название'),
            'value' => Yii::t('app', 'Значение'),
            'note' => Yii::t('app', 'Примечания'),
            'weight' => Yii::t('app', 'Порядок следования'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * Changes order of the items
     *
     * @param integer $newPosition
     * @param bool $save If true, saving of [[weight]] attribute occurs at the end of this method, else the new value of
     * [[weight]] attribute must be saved separately outside of this method
     */
    public function changeOrder($newPosition, $save = true)
    {
        $oldPosition = $this->getOldAttribute('weight');

        $trans = $this->db->beginTransaction();

        try {
            if ($newPosition > $oldPosition) {
                // new position greater than old position,
                // so all positions from old position + 1 up to and including new position should decrement
                $this->updateAllCounters(['weight' => -1], ['between', 'weight', $oldPosition + 1, $newPosition]);
            } else {
                // new position smaller than or equal to old position,
                // so all positions from new position up to and including old position - 1 should increment
                $this->updateAllCounters(['weight' => 1], ['between', 'weight', $newPosition, $oldPosition - 1]);
            }

            if ($save) {
                $this->weight = $newPosition;
                $this->save(false, ['weight']);
            }
            $trans->commit();
        }
        catch (\Exception $e) {
            $trans->rollBack();
        }
    }

    /**
     * Returns the list of statuses
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'Не опубликовано',
            self::STATUS_ACTIVE => 'Опубликовано',
        ];
    }

    /**
     * Returns status name by Id
     *
     * @param $index the status Id
     * @return mixed
     */
    public static function getStatusName($index)
    {
        $options = self::getStatusOptions();

        return $options[$index];
    }

    /**
     * Returns the list of the contact items types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PHONE => 'Телефон',
            self::TYPE_FAX => 'Факс',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_ADDRESS => 'Адрес',
            self::TYPE_OTHER => 'Другое',
        ];
    }

    /**
     * Returns type name by Id
     *
     * @param $index the type Id
     * @return mixed
     */
    public static function getTypeName($index)
    {
        $options = self::getTypes();

        return $options[$index];
    }
}
