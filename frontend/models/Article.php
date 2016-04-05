<?php

namespace frontend\models;

use Yii;
use backend\behaviors\ImagesBehavior;
use common\components\ActiveRecord;
use common\models\Image;

/**
 * This is the model class for table "{{%article}}".
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
 * @property \common\models\Image $mainPhoto
 */
class Article extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
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

    public function behaviors()
    {
        return [
            'photo' => [
                'class' => ImagesBehavior::className(),
                'model' => 'article',
                'ownerIdAttribute' => 'id',
            ],
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

    public function getMainPhoto()
    {
        return $this->hasOne(Image::className(), ['owner_id' => 'id'])->andWhere(['model' => 'article'])->andWhere(['ctg_id' => 0])->andWhere(['is_main' => Image::IS_MAIN]);
    }

    public function getImages()
    {
        return $this->hasMany(Image::className(), ['owner_id' => 'id'])->andWhere(['model' => 'gallery'])->andWhere(['ctg_id' => 0])->andWhere(['status' => Image::STATUS_ACTIVE]);
    }

    /**
     * @param $quantity int quantity of last article
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLastArticles($quantity)
    {
        return Article::find()->where(['status' => Article::STATUS_ACTIVE])->orderBy('published_date DESC')->limit($quantity)->all();
    }
}
