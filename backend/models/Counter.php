<?php

namespace backend\models;

use Yii;
use yii\base\InvalidParamException;

/**
 * This is the model class for table "{{%counter}}".
 *
 * @property string $name
 * @property integer $value
 */
class Counter extends \yii\db\ActiveRecord
{
    protected $internalUpdateFlag = false;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%counter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Название счетчика'),
            'value' => Yii::t('app', 'Значение'),
        ];
    }

    /**
     * @param string $counterName
     * @return int
     */
    public static function getNextNumber($counterName)
    {
        $model = static::find()->where(['name' => $counterName])->one();
        /* @var $model Counter */
        if (is_null($model))
        {
            throw new InvalidParamException('No counter with name "' . $counterName . '"');
        }

        return $model->value;
    }

    public static function incrementValue($counterName)
    {
        $model = static::find()->where(['name' => $counterName])->one();
        /* @var $model Counter */
        if (is_null($model))
        {
            throw new InvalidParamException('No counter with name "' . $counterName . '"');
        }

        return $model->updateCounters(['value' => 1]);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return false;
    }

    public function insert($runValidation = true, $attributes = null)
    {
        return false;
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        return false;
    }

    public function updateInternal($attributes = null)
    {
        return false;
    }

    public function delete()
    {
        return false;
    }
}
