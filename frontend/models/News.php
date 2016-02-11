<?php

namespace app\models;

use Yii;
use common\components\ActiveRecord;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property string $id
 * @property string $name
 * @property string $published_date
 * @property string $intro
 * @property string $content
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $created_date
 * @property string $last_update_date
 * @property string $status
 */
class News extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'published_date', 'intro', 'content', 'created_date', 'last_update_date'], 'required'],
            [['published_date', 'created_date', 'last_update_date'], 'safe'],
            [['intro', 'content'], 'string'],
            [['status'], 'integer'],
            [['name', 'meta_title', 'meta_description', 'meta_keywords'], 'string', 'max' => 255]
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
            'published_date' => Yii::t('app', 'Дата публикации'),
            'intro' => Yii::t('app', 'Вводный текст'),
            'content' => Yii::t('app', 'Текст'),
            'meta_title' => Yii::t('app', 'META Title'),
            'meta_description' => Yii::t('app', 'META Description'),
            'meta_keywords' => Yii::t('app', 'META Keywords'),
            'created_date' => Yii::t('app', 'Дата создания'),
            'last_update_date' => Yii::t('app', 'Дата последнего обновления'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * @param $quantity int quantity of last news
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLastNews($quantity)
    {
        return News::find()->where(['status' => News::STATUS_ACTIVE])->orderBy('published_date DESC')->limit($quantity)->all();
    }
}
