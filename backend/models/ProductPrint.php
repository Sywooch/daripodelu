<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%product_print}}".
 *
 * @property integer $product_id
 * @property string $print_id
 *
 * @property Product $product
 * @property Print $print
 */
class ProductPrint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_print}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'print_id'], 'required'],
            [['product_id'], 'integer'],
            [['print_id'], 'string', 'max' => 20],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['print_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrintKind::className(), 'targetAttribute' => ['print_id' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'ID товара'),
            'print_id' => Yii::t('app', 'ID вида печати'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrint()
    {
        return $this->hasOne(PrintKind::className(), ['name' => 'print_id']);
    }
}
