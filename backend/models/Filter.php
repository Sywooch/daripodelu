<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%filter}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type_id
 *
 * @property FilterType $type
 * @property ProductFilter[] $productFilters
 */
class Filter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%filter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'type_id'], 'required'],
            [['id', 'type_id'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterType::className(), 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID фильтра'),
            'name' => Yii::t('app', 'Название'),
            'type_id' => Yii::t('app', 'ID типа фильтра'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(FilterType::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductFilters()
    {
        return $this->hasMany(ProductFilter::className(), ['type_id' => 'type_id', 'filter_id' => 'id']);
    }
}
