<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%catalogue_product}}".
 *
 * @property integer $catalogue_id
 * @property integer $product_id
 * @property integer $user_row
 *
 * @property Catalogue $catalogue
 * @property Product $product
 */
class CatalogueProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%catalogue_product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catalogue_id', 'product_id'], 'required'],
            [['catalogue_id', 'product_id', 'user_row'], 'integer'],
            [['catalogue_id', 'product_id'], 'unique', 'targetAttribute' => ['catalogue_id', 'product_id'], 'message' => 'The combination of ID категории and ID товара has already been taken.'],
            [['catalogue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogue::className(), 'targetAttribute' => ['catalogue_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'catalogue_id' => Yii::t('app', 'ID категории'),
            'product_id' => Yii::t('app', 'ID товара'),
            'user_row' => Yii::t('app', 'Создан пользователем'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogue()
    {
        return $this->hasOne(Catalogue::className(), ['id' => 'catalogue_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
