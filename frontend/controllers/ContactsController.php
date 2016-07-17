<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use common\models\SEOInformation;
use frontend\models\ContactsItem;
use frontend\models\FeedbackForm;
use frontend\models\Map;

class ContactsController extends \yii\web\Controller
{
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $feedbackModel = new FeedbackForm();
            $this->getView()->params['feedbackModel'] = $feedbackModel;

            return true;
        }

        return false;
    }

    public function actionIndex()
    {
        $this->heading = Yii::t('app', 'Contacts');
        $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = Yii::$app->config->siteMetaDescript;
        $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

        $seoInfo = SEOInformation::findModel('contacts', 'index');
        if ( !is_null($seoInfo)) {
            $this->heading = ($seoInfo->heading == '') ? $this->heading : $seoInfo->heading;
            $this->metaTitle = ($seoInfo->meta_title == '') ? $this->metaTitle : $seoInfo->meta_title;
            $this->metaDescription = ($seoInfo->meta_description == '') ? $this->metaDescription : $seoInfo->meta_description;
            $this->metaKeywords = ($seoInfo->meta_keywords == '') ? $this->metaKeywords : $seoInfo->meta_keywords;
        }

        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $this->metaDescription,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $this->metaKeywords,
        ]);
        $this->view->title = $this->metaTitle;

        $model = new ContactsItem();

        return $this->render('index', [
            'heading' => $this->heading,
            'items' => $model->getItems(),
            'map' => Map::findByController('contacts', 'index'),
        ]);
    }
}
