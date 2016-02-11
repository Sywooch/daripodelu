<?php

namespace frontend\models;

use yii;
use common\models\Image;

/**
 * This is the model class for table "{{%catalogue}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $uri
 *
 * @property Product[] $products
 */
class Catalogue extends yii\db\ActiveRecord
{
    public $products_count;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%catalogue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'uri'], 'required'],
            [['id', 'parent_id'], 'integer'],
            [['name', 'uri'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID категории'),
            'parent_id' => Yii::t('app', 'ID родительской категории'),
            'name' => Yii::t('app', 'Название'),
            'uri' => Yii::t('app', 'URI'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['catalogue_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainProducts()
    {
        return $this->hasMany(Product::className(), ['catalogue_id' => 'id'])->andWhere(['group_id' => null])->orderBy(['status_id' => SORT_ASC, 'id' => SORT_DESC]);
    }

    public function getPhoto()
    {
        return $this->hasOne(Image::className(), ['owner_id' => 'id'])->andWhere(['model' => 'catalogue'])->andWhere(['ctg_id' => 0])->orderBy(['is_main' => SORT_DESC, 'owner_id' => SORT_ASC]);
    }
}
