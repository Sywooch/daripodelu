<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%slave_product}}".
 *
 * @property integer $id
 * @property integer $parent_product_id
 * @property string $code
 * @property string $name
 * @property string $size_code
 * @property double $weight
 * @property double $price
 * @property string $price_currency
 * @property string $price_name
 * @property integer $amount
 * @property integer $free
 * @property integer $inwayamount
 * @property integer $inwayfree
 * @property double $enduserprice
 * @property integer $user_row
 *
 * @property Product $parentProduct
 */
class SlaveProduct extends \yii\db\ActiveRecord
{
    const IS_USER_ROW = 1;

    const SCENARIO_INSERT = 'insert';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%slave_product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_product_id', 'user_row'], 'required'],
            [['id', 'parent_product_id', 'amount', 'free', 'inwayamount', 'inwayfree', 'user_row'], 'integer'],
            [['weight', 'price', 'enduserprice'], 'number'],
            [['code'], 'string', 'max' => 100],
            [['name', 'size_code'], 'string', 'max' => 255],
            [['price_currency'], 'string', 'max' => 20],
            [['price_name'], 'string', 'max' => 40],
            [['amount', 'free', 'inwayamount', 'inwayfree'], 'default', 'value' => 0, 'on' => static::SCENARIO_INSERT],
            [['weight', 'price', 'enduserprice'], 'default', 'value' => 0.00, 'on' => static::SCENARIO_INSERT],
            [['parent_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['parent_product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID товара'),
            'parent_product_id' => Yii::t('app', 'Родительский товар'),
            'code' => Yii::t('app', 'Артикул'),
            'name' => Yii::t('app', 'Название'),
            'size_code' => Yii::t('app', 'Размер'),
            'weight' => Yii::t('app', 'Вес'),
            'price' => Yii::t('app', 'Цена'),
            'price_currency' => Yii::t('app', 'Валюта'),
            'price_name' => Yii::t('app', 'Название цены'),
            'amount' => Yii::t('app', 'Всего на складе'),
            'free' => Yii::t('app', 'Доступно для резервирования'),
            'inwayamount' => Yii::t('app', 'Всего в пути (поставка)'),
            'inwayfree' => Yii::t('app', 'Доступно для резервирования из поставки'),
            'enduserprice' => Yii::t('app', 'Цена для конечного пользователя'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'parent_product_id']);
    }

    public function afterFind()
    {
        $this->price = yii::$app->formatter->asDecimal($this->price, 2, [\NumberFormatter::GROUPING_USED => 0]);
        $this->enduserprice = yii::$app->formatter->asDecimal($this->enduserprice, 2, [\NumberFormatter::GROUPING_USED => 0]);
        parent::afterFind();
    }
}
