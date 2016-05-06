<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%product_attachment}}".
 *
 * @property integer $product_id
 * @property integer $meaning тип файла (1 - картинка, 0 - НЕ картинка)
 * @property string $file
 * @property string $image
 * @property string $name
 * @property string $imageUrl
 * @property string $fileUrl
 *
 * @property Product $product
 */
class ProductAttachment extends \yii\db\ActiveRecord
{
    const IS_FILE = 0;
    const IS_IMAGE = 1;

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

    public function getImageUrl()
    {
        return $this->meaning == static::IS_IMAGE ? yii::$app->params['baseUploadURL'] . '/' . $this->product_id . '/' . $this->image : '';
    }

    public function getFileUrl()
    {
        return $this->meaning == static::IS_FILE ? yii::$app->params['baseUploadURL'] . '/' . $this->product_id . '/' . $this->file : '';
    }
}
