<?php

namespace common\models;

use Yii;
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

    const ITEM_CATEGORY = 'category';
    const ITEM_PRODUCT = 'product';
    const ITEM_FILTER_TYPE = 'filter_type';
    const ITEM_FILTER = 'filter';
    const ITEM_SLAVE_PRODUCT = 'slave_product';
    const ITEM_STOCK = 'stock';

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
            'created_date' => Yii::t('app', 'Дата создания'),
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
}
