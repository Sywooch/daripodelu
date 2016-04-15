<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property integer $id
 * @property integer $catalogue_id
 * @property integer $group_id
 * @property string $code
 * @property string $name
 * @property string $product_size
 * @property string $matherial
 * @property string $small_image
 * @property string $big_image
 * @property string $super_big_image
 * @property string $content
 * @property integer $status_id
 * @property string $status_caption
 * @property string $brand
 * @property double $weight
 * @property integer $pack_amount
 * @property double $pack_weigh
 * @property double $pack_volume
 * @property double $pack_sizex
 * @property double $pack_sizey
 * @property double $pack_sizez
 * @property integer $amount
 * @property integer $free
 * @property integer $inwayamount
 * @property integer $inwayfree
 * @property double $enduserprice
 * @property integer $user_row
 *
 * @property Catalogue $catalogue
 * @property ProductAttachment[] $productAttachments
 * @property ProductFilter[] $productFilters
 * @property ProductPrint[] $productPrints
 */
class Product extends \yii\db\ActiveRecord
{
    const CREATE_AUTOMATIC = 0;
    const CREATE_BY_USER = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catalogue_id', 'group_id', 'status_id', 'pack_amount', 'amount', 'free', 'inwayamount', 'inwayfree', 'user_row'], 'integer'],
            [['catalogue_id', 'group_id', 'name', 'product_size', 'matherial', 'status_id', 'status_caption', 'brand', 'weight'], 'required'],
            [['content'], 'string'],
            [['weight', 'pack_weigh', 'pack_volume', 'pack_sizex', 'pack_sizey', 'pack_sizez', 'enduserprice'], 'number'],
            [['code'], 'string', 'max' => 100],
            [['name', 'product_size', 'matherial', 'small_image', 'big_image', 'super_big_image'], 'string', 'max' => 255],
            [['status_caption'], 'string', 'max' => 40],
            [['brand'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID товара'),
            'catalogue_id' => Yii::t('app', 'Категория'),
            'group_id' => Yii::t('app', 'ID группы'),
            'code' => Yii::t('app', 'Артикул'),
            'name' => Yii::t('app', 'Название'),
            'product_size' => Yii::t('app', 'Размер'),
            'matherial' => Yii::t('app', 'Материал'),
            'small_image' => Yii::t('app', 'Путь к файлу картинки 200х200'),
            'big_image' => Yii::t('app', 'Путь к файлу картинки 280х280'),
            'super_big_image' => Yii::t('app', 'Путь к файлу картинки 1000х1000'),
            'content' => Yii::t('app', 'Описание'),
            'status_id' => Yii::t('app', 'ID статуса'),
            'status_caption' => Yii::t('app', 'Статус'),
            'brand' => Yii::t('app', 'Бренд'),
            'weight' => Yii::t('app', 'Вес'),
            'pack_amount' => Yii::t('app', 'Количество в упаковке'),
            'pack_weigh' => Yii::t('app', 'Вес упаковки'),
            'pack_volume' => Yii::t('app', 'Объем упаковки'),
            'pack_sizex' => Yii::t('app', 'Ширина упаковки'),
            'pack_sizey' => Yii::t('app', 'Высота упаковки'),
            'pack_sizez' => Yii::t('app', 'Глубина упаковки'),
            'amount' => Yii::t('app', 'Всего на складе'),
            'free' => Yii::t('app', 'Доступно для резервирования'),
            'inwayamount' => Yii::t('app', 'Всего в пути (поставка)'),
            'inwayfree' => Yii::t('app', 'Доступно для резервирования из поставки'),
            'enduserprice' => Yii::t('app', 'Цена End-User'),
            'user_row' => Yii::t('app', 'Метод создания'),
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
    public function getProductAttachments()
    {
        return $this->hasMany(ProductAttachment::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductFilters()
    {
        return $this->hasMany(ProductFilter::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrints()
    {
        return $this->hasMany(ProductPrint::className(), ['product_id' => 'id']);
    }
}
