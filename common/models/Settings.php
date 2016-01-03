<?php

namespace common\models;

use common\components;
use common\components\ActiveRecord;
use yii;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property string $id
 * @property string $param
 * @property string $value
 * @property string $default
 * @property string $label
 * @property string $type
 */
class Settings extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param', 'label', 'type'], 'required'],
            [['value', 'default'], 'string'],
            [['param'], 'string', 'max' => 128],
            [['label'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 64],
            [['param'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'param' => Yii::t('app', 'Имя переменной'),
            'value' => Yii::t('app', 'Значение'),
            'default' => Yii::t('app', 'По умолчанию'),
            'label' => Yii::t('app', 'Название'),
            'type' => Yii::t('app', 'Тип'),
        ];
    }

    public function getParams()
    {
        return $this->find()->all();
    }

    public function getParamsAssoc()
    {
        $params = [];
        $items = $this->getParams();

        /* @var $item Settings*/
        foreach ($items as $item)
        {
            $params[$item->param] = $item;
        }

        return $params;
    }

    public function getParam($name)
    {
        return $this->find()->where(['param' => $name])->one();
    }
}
