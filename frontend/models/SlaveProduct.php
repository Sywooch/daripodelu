<?php

namespace frontend\models;

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
 *
 * @property Product $parentProduct
 */
class SlaveProduct extends \yii\db\ActiveRecord
{
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
            [['id', 'parent_product_id'], 'required'],
            [['id', 'parent_product_id', 'amount', 'free', 'inwayamount', 'inwayfree'], 'integer'],
            [['weight', 'price', 'enduserprice'], 'number'],
            [['code'], 'string', 'max' => 100],
            [['name', 'size_code'], 'string', 'max' => 255],
            [['price_currency'], 'string', 'max' => 20],
            [['price_name'], 'string', 'max' => 40]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID товара'),
            'parent_product_id' => Yii::t('app', 'ID родительского товара'),
            'code' => Yii::t('app', 'Артикул'),
            'name' => Yii::t('app', 'Название'),
            'size_code' => Yii::t('app', 'Размер'),
            'weight' => Yii::t('app', 'Вес'),
            'price' => Yii::t('app', 'Цена'),
            'price_currency' => Yii::t('app', 'Валюта'),
            'price_name' => Yii::t('app', 'Название цены'),
            'amount' => Yii::t('app', 'Amount'),
            'free' => Yii::t('app', 'Free'),
            'inwayamount' => Yii::t('app', 'Inwayamount'),
            'inwayfree' => Yii::t('app', 'Inwayfree'),
            'enduserprice' => Yii::t('app', 'Enduserprice'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'parent_product_id']);
    }
}
