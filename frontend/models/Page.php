<?php

namespace app\models;

use Yii;
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
 * @method \common\models\MenuTree getAliasModel
 */
class Page extends \common\components\ActiveRecord
{
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
            [['name', 'content', 'last_update_date', 'created_date'], 'required'],
            [['content'], 'string'],
            [['last_update_date', 'created_date'], 'safe'],
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
            'id' => Yii::t('app', 'IВ'),
            'name' => Yii::t('app', 'Название'),
            'content' => Yii::t('app', 'Содержимое'),
            'meta_title' => Yii::t('app', 'META Title'),
            'meta_description' => Yii::t('app', 'META Description'),
            'meta_keywords' => Yii::t('app', 'META Keywords'),
            'last_update_date' => Yii::t('app', 'Дата последнего обновления'),
            'created_date' => Yii::t('app', 'Дата создания'),
            'status' => Yii::t('app', 'Опубликовать'),
        ];
    }
}
