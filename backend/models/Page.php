<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property string $id
 * @property string $name
 * @property string $content
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $last_update_date
 * @property string $created_date
 * @property integer $status
 */
class Page extends \common\components\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['content'], 'string'],
            [['content', 'last_update_date', 'created_date'], 'safe'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => Page::STATUS_ACTIVE],
            [['name', 'content', 'meta_title', 'meta_description', 'meta_keywords'], 'trim'],
            [['name', 'meta_title', 'meta_description', 'meta_keywords'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'content' => Yii::t('app', 'Содержимое'),
            'meta_title' => Yii::t('app', 'Meta Title'),
            'meta_description' => Yii::t('app', 'Meta Description'),
            'meta_keywords' => Yii::t('app', 'Meta Keywords'),
            'last_update_date' => Yii::t('app', 'Дата последнего обновления'),
            'created_date' => Yii::t('app', 'Дата создания'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_date', 'last_update_date'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_update_date'],
                ],
                'value' => function ()
                {
                    $dateTime = new \DateTime(null, new \DateTimeZone(Yii::$app->formatter->timeZone));
                    $timeOffset = $dateTime->getOffset();
                    $timeStamp = Yii::$app->formatter->asTimestamp(time());

                    return date('Y-m-d H:i:s', $timeStamp - $timeOffset);
                },//new Expression('NOW()'),
            ],
        ];
    }
}
