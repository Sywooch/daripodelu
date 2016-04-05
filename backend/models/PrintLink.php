<?php

namespace backend\models;

use yii;
use backend\models\PrintKind;

/**
 * This is the model class for table "{{%print_link}}".
 *
 * @property string $code
 * @property string $link
 *
 * @property Print $code0
 */
class PrintLink extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%print_link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'link'], 'required'],
            [['code'], 'string', 'max' => 20],
            [['link'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['code'], 'exist', 'skipOnError' => true, 'targetClass' => PrintKind::className(), 'targetAttribute' => ['code' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Код'),
            'link' => Yii::t('app', 'Ссылка'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCode0()
    {
        return $this->hasOne(PrintKind::className(), ['name' => 'code']);
    }
}
