<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%filter_type}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Filter[] $filters
 */
class FilterType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%filter_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID типа фильтра'),
            'name' => Yii::t('app', 'Название типа'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(Filter::className(), ['type_id' => 'id']);
    }
}
