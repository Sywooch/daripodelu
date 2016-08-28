<?php

namespace common\models;

use Yii;
use yii\base\InvalidParamException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%update_log}}".
 *
 * @property integer $id
 * @property integer $status
 * @property string $action
 * @property string $item
 * @property integer $item_id
 * @property string $message
 * @property string $created_date
 */
class UpdateGiftsDBLog extends \yii\db\ActiveRecord
{
    const STATUS_INFO = 1;
    const STATUS_SUCCESS = 10;
    const STATUS_WARNING = 20;
    const STATUS_ERROR = 30;

    const ACTION_INSERT = 'insert';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_LOAD = 'load';
    const ACTION_PARSING = 'parsing';

    const TYPE_CATEGORY = 'category';
    const TYPE_CATEGORY_PRODUCT_REL = 'category_product_rel';
    const TYPE_PRODUCT = 'product';
    const TYPE_FILTER_TYPE = 'filter_type';
    const TYPE_FILTER = 'filter';
    const TYPE_PRODUCT_PRINT_REL = 'product_print_rel';
    const TYPE_PRODUCT_FILTER_REL = 'product_filter_rel';
    const TYPE_SLAVE_PRODUCT = 'slave_product';
    const TYPE_PRINT = 'print';
    const TYPE_STOCK = 'stock';
    const TYPE_PRODUCT_ATTACH = 'product_attachment';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%update_db_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'action', 'item', 'message'], 'required'],
            [['status', 'item_id'], 'integer'],
            [['created_date'], 'safe'],
            [['action', 'item'], 'string', 'max' => 20],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID записи'),
            'status' => Yii::t('app', 'Статус'),
            'action' => Yii::t('app', 'Действие'),
            'item' => Yii::t('app', 'Элемент'),
            'item_id' => Yii::t('app', 'ID элемента'),
            'message' => Yii::t('app', 'Сообщение'),
            'created_date' => Yii::t('app', 'Дата создания записи'),
        ];
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * Behaviors declared in this method will be attached to the component automatically (on demand).
     *
     * @return array the behavior configurations.
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_date',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Returns a list of statuses that can be taken
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_INFO => 'Информ. сообщение',
            static::STATUS_SUCCESS => 'Успешное действие',
            static::STATUS_WARNING => 'Предупреждение',
            static::STATUS_ERROR => 'Ошибка',
        ];
    }

    /**
     * Returns the status name for $statusId
     * @param int $statusId
     * @return string
     */
    public static function getStatusName($statusId)
    {
        $statuses = static::getStatuses();

        if ( !isset($statuses[$statusId])) {
            throw new InvalidParamException("Unknown status: $statusId");
        }

        return $statuses[$statusId];
    }

    /**
     * Returns a list of actions, which may be registered in the logs
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            static::ACTION_INSERT => 'Добавление',
            static::ACTION_UPDATE => 'Обновление',
            static::ACTION_DELETE => 'Удаление',
            static::ACTION_LOAD => 'Загрузка',
            static::ACTION_PARSING => 'Парсирование',
        ];
    }

    /**
     * Returns the action name for $actionId
     *
     * @param string $actionId
     * @return string
     */
    public static function getActionName($actionId)
    {
        $actions = static::getActions();

        if ( !isset($actions[$actionId])) {
            throw new InvalidParamException("Unknown action: $actionId");
        }

        return $actions[$actionId];
    }

    /**
     * Returns a list of types of log records
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_CATEGORY => 'Категория',
            static::TYPE_FILTER_TYPE => 'Фильтр',
            static::TYPE_FILTER => 'Значение фильтра',
            static::TYPE_PRINT => 'Метод нанесения',
            static::TYPE_PRODUCT => 'Товар',
            static::TYPE_PRODUCT_ATTACH => 'Доп. файлы',
            static::TYPE_PRODUCT_FILTER_REL => 'Связь "Товар - Фильтр"',
            static::TYPE_PRODUCT_PRINT_REL => 'Связь "Товар - Метод нанес."',
            static::TYPE_SLAVE_PRODUCT => 'Подчиненный товар',
            static::TYPE_STOCK => 'stock.xml',
        ];
    }

    /**
     * Returns the type name for $typeId
     *
     * @param string $typeId
     * @return string
     */
    public static function getTypeName($typeId)
    {
        $types = static::getTypes();

        if ( !isset($types[$typeId])) {
            throw new InvalidParamException("Unknown type: $typeId");
        }

        return $types[$typeId];
    }
}
