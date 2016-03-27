<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $fio
 * @property string $phone
 * @property string $email
 * @property string $data
 * @property string $order_date
 * @property integer $status
 */
abstract class Order extends ActiveRecord
{
    /**
     * The new order
     */
    const STATUS_NEW = 0;
    /**
     * The order in waiting8
     */
    const STATUS_WAIT = 1;
    /**
     * The order processed
     */
    const STATUS_PROCESSED = 3;
    /**
     * The order canceled
     */
    const STATUS_CANCELED = 99;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'phone', 'email'], 'trim'],
            [['fio', 'phone', 'email', 'data'], 'required'],
            [['status'], 'integer'],
            [['data'], 'string'],
            [['fio', 'phone', 'email'], 'string', 'max' => 255],
            [['status', 'order_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID заказа'),
            'fio' => Yii::t('app', 'ФИО'),
            'phone' => Yii::t('app', 'Контактный телефон клиента'),
            'email' => Yii::t('app', 'Электронная почта клиента'),
            'data' => Yii::t('app', 'Информация о заказе'),
            'order_date' => Yii::t('app', 'Дата и время заказа'),
            'status' => Yii::t('app', 'Статус заказа'),
        ];
    }

    public function getDirPath()
    {
        return yii::$app->params['uploadPath'] . '/orders/' . $this->getDirPathPart($this->id);
    }

    public function getDirUrl()
    {
        return yii::$app->params['baseUploadURL'] . '/orders/' . $this->getDirPathPart($this->id);
    }

    protected function getDirPathPart($string)
    {
        $md5String = md5($string);
        $dirName = mb_substr($md5String, 1, 2);

        return $dirName;
    }
}
