<?php

namespace app\models;

use common\models\Settings;
use Yii;
use yii\base\Model;

/**
 * Settings form
 */
class SettingsForm extends Model {

    public $siteName;
    public $siteAdminEmail;
    public $siteMetaDescription;
    public $siteMetaKeywords;
    public $newsPerPage;
    public $newsPerHome;
    public $gateLogin;
    public $gatePassword;

    public function rules()
    {
        return [
            [['siteName', 'siteAdminEmail', 'siteMetaDescription', 'siteMetaKeywords', 'gateLogin', 'gatePassword'], 'string'],
            [['siteName', 'newsPerPage', 'newsPerHome', 'gateLogin', 'gatePassword'], 'required'],
            [['newsPerPage', 'newsPerHome'], 'integer'],
            ['siteAdminEmail', 'email'],
            [['siteName', 'siteAdminEmail', 'siteMetaDescription', 'siteMetaKeywords', 'newsPerPage', 'newsPerHome', 'gateLogin', 'gatePassword'], 'trim'],
        ];
    }

    /**
     * Fills attributes of settings form by values
     *
     * @return SettingsForm
     */
    public static function fillAttributes()
    {
        $model = new SettingsForm();

        $model->siteName = Yii::$app->config->get('SITE_NAME');
        $model->siteAdminEmail = Yii::$app->config->get('SITE_ADMIN_EMAIL');
        $model->siteMetaDescription = Yii::$app->config->get('SITE_META_DESCRIPT');
        $model->siteMetaKeywords = Yii::$app->config->get('SITE_META_KEYWORDS');
        $model->newsPerPage = Yii::$app->config->get('NEWS_ITEMS_PER_PAGE');
        $model->newsPerHome = Yii::$app->config->get('NEWS_ITEMS_PER_HOME');
        $model->gateLogin = Yii::$app->config->get('GATE_LOGIN');
        $model->gatePassword = Yii::$app->config->get('GATE_PASSWORD');

        return $model;
    }

    public function attributeLabels()
    {
        $model = new Settings();
        $items = $model->getParamsAssoc();

        return [
            'siteName' => $items['SITE_NAME']->label,
            'siteAdminEmail' => $items['SITE_ADMIN_EMAIL']->label,
            'siteMetaDescription' => $items['SITE_META_DESCRIPT']->label,
            'siteMetaKeywords' => $items['SITE_META_KEYWORDS']->label,
            'newsPerPage' => $items['NEWS_ITEMS_PER_PAGE']->label,
            'newsPerHome' => $items['NEWS_ITEMS_PER_HOME']->label,
            'gateLogin' => $items['GATE_LOGIN']->label,
            'gatePassword' => $items['GATE_PASSWORD']->label,
        ];
    }

    public function set($key, $value)
    {
        $rslt = false;
        $model = Settings::findOne(['param' => $key]);
        if ($model)
        {
            $model->value = $value;
            $rslt = $model->save();
        }

        return $rslt;
    }

    public function save()
    {
        $rslt = ($this->isAttributeRequired('siteName')? $this->set('SITE_NAME', $this->siteName) : true) &&
                ($this->isAttributeRequired('siteAdminEmail')? $this->set('SITE_ADMIN_EMAIL', $this->siteAdminEmail) : true) &&
                ($this->isAttributeRequired('siteMetaDescription')? $this->set('SITE_META_DESCRIPT', $this->siteMetaDescription) : true) &&
                ($this->isAttributeRequired('siteMetaKeywords')? $this->set('SITE_META_KEYWORDS', $this->siteMetaKeywords) : true) &&
                ($this->isAttributeRequired('newsPerPage')? $this->set('NEWS_ITEMS_PER_PAGE', $this->newsPerPage) : true) &&
                ($this->isAttributeRequired('newsPerHome')? $this->set('NEWS_ITEMS_PER_HOME', $this->newsPerHome) : true) &&
                ($this->isAttributeRequired('gateLogin')? $this->set('GATE_LOGIN', $this->gateLogin) : true) &&
                ($this->isAttributeRequired('gatePassword')? $this->set('GATE_PASSWORD', $this->gatePassword) : true);

        return $rslt;
    }
}