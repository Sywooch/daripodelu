<?php

namespace frontend\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property integer $id
 * @property resource $data
 * @property string $last_use_date
 */
class Cart extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'required'],
            [['data'], 'string'],
            [['last_use_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Data'),
            'last_use_date' => Yii::t('app', 'Last Use Date'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => ['last_use_date'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function removeExpired()
    {
        $secondsNumber = yii::$app->params['cartCookieValidityPeriod'];

        return static::deleteAll(['<', 'last_use_date', new Expression('NOW() - INTERVAL ' . $secondsNumber . ' SECOND')]);
    }
}
