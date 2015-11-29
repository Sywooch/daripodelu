<?php

namespace common\models;

use yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%seo}}".
 *
 * @property integer $id
 * @property string $module_id
 * @property string $controller_id
 * @property string $action_id
 * @property integer $ctg_id
 * @property integer $item_id
 * @property string $heading
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 */
class SEOInformation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['controller_id', 'action_id'], 'required'],
            [['ctg_id', 'item_id'], 'integer'],
            [['module_id', 'controller_id', 'action_id'], 'string', 'max' => 40],
            [['heading', 'meta_title', 'meta_description', 'meta_keywords'], 'string', 'max' => 255],
            [['heading', 'meta_title', 'meta_description', 'meta_keywords'], 'trim', 'skipOnEmpty' => true],
            [['module_id', 'controller_id', 'action_id', 'ctg_id', 'item_id'], 'unique', 'targetAttribute' => ['module_id', 'controller_id', 'action_id', 'ctg_id', 'item_id'], 'message' => 'The combination of Модуль, Контроллер, Действие, Категория and Элемент has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'module_id' => Yii::t('app', 'Модуль'),
            'controller_id' => Yii::t('app', 'Контроллер'),
            'action_id' => Yii::t('app', 'Действие'),
            'ctg_id' => Yii::t('app', 'Категория'),
            'item_id' => Yii::t('app', 'Элемент'),
            'heading' => Yii::t('app', 'Заголовок'),
            'meta_title' => Yii::t('app', 'META Title'),
            'meta_description' => Yii::t('app', 'META Description'),
            'meta_keywords' => Yii::t('app', 'META Keywords'),
        ];
    }

    /**
     * @param string $controller название контроллера
     * @param string $action название действия
     * @param integer $itemId ID элемента. По умолчанию null
     * @param integer $ctgId ID категории. По умолчанию null
     * @param string $module название модуля. По умолчанию null
     * @return null|SEOInformation
     */
    public static function findModel($controller, $action, $itemId = null, $ctgId = null, $module = null)
    {
        return static::find()->where(['module_id' => $module, 'controller_id' => $controller, 'action_id' => $action, 'item_id' => $itemId, 'ctg_id' => $ctgId])->one();
    }
}
