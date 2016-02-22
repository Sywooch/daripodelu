<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%product_attachment}}".
 *
 * @property integer $product_id
 * @property integer $meaning
 * @property string $file
 * @property string $image
 * @property string $name
 *
 * @property Product $product
 */
class ProductAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'meaning'], 'required'],
            [['product_id', 'meaning'], 'integer'],
            [['file', 'image', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'ID товара'),
            'meaning' => Yii::t('app', 'Тип файла'),
            'file' => Yii::t('app', 'URL доп. файла'),
            'image' => Yii::t('app', 'URL доп. картинки'),
            'name' => Yii::t('app', 'Описание'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
