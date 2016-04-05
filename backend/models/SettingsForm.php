<?php

namespace app\models;

use common\models\Settings;
use Yii;
use yii\base\Model;

/**
 * Settings form
 */
class SettingsForm extends Model
{

    public $siteName;
    public $siteAdminEmail;
    public $siteEmail;
    public $siteMetaDescription;
    public $siteMetaKeywords;
    public $sitePhone;
    public $siteWorkSchedule;
    public $newsPerPage;
    public $newsPerHome;
    public $articlesPerPage;
    public $articlesPerHome;
    public $productsPerPage;
    public $gateLogin;
    public $gatePassword;
    private $conformityVars = [
        'siteName' => 'SITE_NAME',
        'siteAdminEmail' => 'SITE_ADMIN_EMAIL',
        'siteEmail' => 'SITE_EMAIL',
        'siteMetaDescription' => 'SITE_META_DESCRIPT',
        'siteMetaKeywords' => 'SITE_META_KEYWORDS',
        'newsPerPage' => 'NEWS_ITEMS_PER_PAGE',
        'newsPerHome' => 'NEWS_ITEMS_PER_HOME',
        'articlesPerPage' => 'ARTICLE_ITEMS_PER_PAGE',
        'articlesPerHome' => 'ARTICLE_ITEMS_PER_HOME',
        'productsPerPage' => 'PRODUCTS_PER_PAGE',
        'gateLogin' => 'GATE_LOGIN',
        'gatePassword' => 'GATE_PASSWORD',
        'sitePhone' => 'SITE_PHONE',
        'siteWorkSchedule' => 'SITE_WORK_SCHEDULE',
    ];

    public function rules()
    {
        return [
            [['siteName', 'siteAdminEmail', 'siteEmail', 'siteMetaDescription', 'siteMetaKeywords', 'gateLogin', 'gatePassword', 'sitePhone', 'siteWorkSchedule'], 'string'],
            [['siteName', 'newsPerPage', 'newsPerHome', 'articlesPerPage', 'articlesPerHome', 'productsPerPage', 'gateLogin', 'gatePassword'], 'required'],
            [['newsPerPage', 'newsPerHome', 'articlesPerPage', 'articlesPerHome', 'productsPerPage'], 'integer'],
            [['siteAdminEmail', 'siteEmail'], 'email'],
            [['siteName', 'siteAdminEmail', 'siteEmail', 'siteMetaDescription', 'siteMetaKeywords', 'newsPerPage', 'newsPerHome', 'articlesPerPage', 'articlesPerHome', 'productsPerPage', 'gateLogin', 'gatePassword', 'sitePhone', 'siteWorkSchedule'], 'trim'],
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
        $model->siteEmail = Yii::$app->config->get('SITE_EMAIL');
        $model->siteMetaDescription = Yii::$app->config->get('SITE_META_DESCRIPT');
        $model->siteMetaKeywords = Yii::$app->config->get('SITE_META_KEYWORDS');
        $model->sitePhone = Yii::$app->config->get('SITE_PHONE');
        $model->siteWorkSchedule = Yii::$app->config->get('SITE_WORK_SCHEDULE');
        $model->newsPerPage = Yii::$app->config->get('NEWS_ITEMS_PER_PAGE');
        $model->newsPerHome = Yii::$app->config->get('NEWS_ITEMS_PER_HOME');
        $model->articlesPerPage = Yii::$app->config->get('ARTICLE_ITEMS_PER_PAGE');
        $model->articlesPerHome = Yii::$app->config->get('ARTICLE_ITEMS_PER_HOME');
        $model->productsPerPage = Yii::$app->config->get('PRODUCTS_PER_PAGE');
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
            'siteEmail' => $items['SITE_EMAIL']->label,
            'siteMetaDescription' => $items['SITE_META_DESCRIPT']->label,
            'siteMetaKeywords' => $items['SITE_META_KEYWORDS']->label,
            'sitePhone' => $items['SITE_PHONE']->label,
            'siteWorkSchedule' => $items['SITE_WORK_SCHEDULE']->label,
            'newsPerPage' => $items['NEWS_ITEMS_PER_PAGE']->label,
            'newsPerHome' => $items['NEWS_ITEMS_PER_HOME']->label,
            'articlesPerPage' => $items['ARTICLE_ITEMS_PER_PAGE']->label,
            'articlesPerHome' => $items['ARTICLE_ITEMS_PER_HOME']->label,
            'productsPerPage' => $items['PRODUCTS_PER_PAGE']->label,
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
        $rslt = true;
        foreach ($this->conformityVars as $param => $var)
        {
            $rslt = $rslt && $this->set($var, $this->{$param});
        }

        return $rslt;
    }
}