<?php

namespace frontend\models;

use yii;

/**
 * This is the model class for table "{{%product_filter}}".
 *
 * @property integer $product_id
 * @property integer $filter_id
 * @property integer $type_id
 *
 * @property Filter $type
 * @property Product $product
 */
class ProductFilter extends yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_filter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'filter_id', 'type_id'], 'required'],
            [['product_id', 'filter_id', 'type_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'ID товара'),
            'filter_id' => Yii::t('app', 'ID фильтра'),
            'type_id' => Yii::t('app', 'Тип фильтра'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Filter::className(), ['type_id' => 'type_id', 'id' => 'filter_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
