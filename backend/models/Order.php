<?php

namespace backend\models;

use Yii;
use common\models\Order as OrderCommon;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $fio
 * @property string $phone
 * @property string $email
 * @property string $data
 * @property array $dataArr
 * @property string $order_date
 * @property integer $status
 */
class Order extends OrderCommon
{
    public $dataArr = [];

    public function rules()
    {
        return array_merge(parent::rules(), [[['dataArr'], 'safe']]);
    }

    public static function getNewOrdersCount()
    {
        return static::find()->where(['status' => static::STATUS_NEW])->count();
    }

    /**
     * @return array user type names indexed by type IDs
     */
    static public function getStatusOptions()
    {
        return array(
            self::STATUS_NEW => 'Новый',
            self::STATUS_WAIT => 'В обработке',
            self::STATUS_PROCESSED => 'Обработан',
            self::STATUS_CANCELED => 'Отменен',
        );
    }

    static public function getStatusName($index)
    {
        $options = self::getStatusOptions();

        return $options[$index];
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->dataArr = unserialize($this->data);
    }
}
