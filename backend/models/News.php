<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use backend\behaviors\ImagesBehavior;
use common\components\ActiveRecord;
use common\models\Image;

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
            [['name', 'published_date', 'content', 'status'], 'required'],
            [['created_date', 'last_update_date'], 'safe'],
            [['name', 'intro', 'content'], 'string'],
            [['status'], 'integer'],
            [['name', 'published_date', 'intro', 'content', 'meta_title', 'meta_description', 'meta_keywords'], 'trim', 'skipOnEmpty' => true],
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
            'meta_title' => Yii::t('app', 'Meta Title'),
            'meta_description' => Yii::t('app', 'Meta Description'),
            'meta_keywords' => Yii::t('app', 'Meta Keywords'),
            'created_date' => Yii::t('app', 'Дата создания'),
            'last_update_date' => Yii::t('app', 'Дата последнего обновления'),
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
                'value' => function () {
                    $dateTime = new \DateTime(null, new \DateTimeZone(Yii::$app->formatter->timeZone));
                    $timeOffset = $dateTime->getOffset();
                    $timeStamp = Yii::$app->formatter->asTimestamp(time());

                    return date('Y-m-d H:i:s', $timeStamp - $timeOffset);
                },
            ],
            'photo' => [
                'class' => ImagesBehavior::className(),
                'model' => 'news',
                'ownerIdAttribute' => 'id',
            ],
        ];
    }

    public function getMainPhoto()
    {
        return $this->hasOne(Image::className(), ['owner_id' => 'id'])->andWhere(['model' => 'news'])->andWhere(['ctg_id' => 0])->andWhere(['is_main' => Image::IS_MAIN]);
    }

    public function getImages()
    {
        return $this->hasMany(Image::className(), ['owner_id' => 'id'])->andWhere(['model' => 'gallery'])->andWhere(['ctg_id' => 0])->andWhere(['status' => Image::STATUS_ACTIVE]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $dateTime = new \DateTime(null, new \DateTimeZone(Yii::$app->formatter->timeZone));
            $timeOffset = $dateTime->getOffset();

            $timeStamp = Yii::$app->formatter->asTimestamp($this->published_date);
            $this->published_date = date('Y-m-d H:i:s', $timeStamp - $timeOffset);

            if ($insert === false) {
                $timeStamp = Yii::$app->formatter->asTimestamp($this->created_date);
                $this->created_date = date('Y-m-d H:i:s', $timeStamp - 2 * $timeOffset);
            }

            return true;
        } else {
            return false;
        }
    }

    function afterFind()
    {
        parent::afterFind();
        $dateTime = new \DateTime(null, new \DateTimeZone(Yii::$app->formatter->timeZone));
        $timeOffset = $dateTime->getOffset();
        $timeStamp = Yii::$app->formatter->asTimestamp($this->published_date);

        $this->published_date = date('Y-m-d H:i:s', $timeStamp - 2 * $timeOffset);
    }
}
