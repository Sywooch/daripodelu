<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%print}}".
 *
 * @property string $name
 * @property string $description
 *
 * @property ProductPrint[] $productPrints
 */
class PrintKind extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%print}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrints()
    {
        return $this->hasMany(ProductPrint::className(), ['print_id' => 'name']);
    }
}
