<?php

namespace frontend\controllers;

use frontend\models\Product;
use yii;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\SEOInformation;
use frontend\models\Catalogue;
use frontend\models\FeedbackForm;

class CatalogueController extends \yii\web\Controller
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action))
        {
            $feedbackModel = new FeedbackForm();
            $this->getView()->params['feedbackModel'] = $feedbackModel;

            return true;
        }

        return false;
    }

    public function actionIndex()
    {
        $categories = Catalogue::find()
            ->with('photo')
            ->where(['parent_id' => 1])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $this->heading = Yii::t('app', 'Catalogue');
        $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = Yii::$app->config->siteMetaDescript;
        $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

        $seoInfo = SEOInformation::findModel('catalogue', 'index');
        if ( ! is_null($seoInfo))
        {
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

        return $this->render('index', [
            'heading' => $this->heading,
            'categories' => $categories,
        ]);
    }

    public function actionView($uri)
    {
        $model = Catalogue::findOne(['uri' => $uri]);

        if (is_null($model))
        {
            throw new NotFoundHttpException();
        }

        $childCategoriesCount = (int) Catalogue::find()->where(['parent_id' => $model->id])->count();

        if ($childCategoriesCount > 0)
        {
            $childCategories = Catalogue::find()
                ->select([
                    '{{%catalogue}}.*',
                    'COUNT({{%product}}.id) as products_count',
                ])
                ->where(['parent_id' => $model->id])
                ->leftJoin('{{%product}}', '{{%catalogue}}.id = {{%product}}.catalogue_id')
                ->groupBy('{{%catalogue}}.id')
                ->orderBy(['{{%catalogue}}.id' => SORT_ASC])
                ->all();

            $ids = ArrayHelper::getColumn($childCategories, 'id');
        }
        else
        {
            $childCategories = Catalogue::find()
                ->select([
                    '{{%catalogue}}.*',
                    'COUNT({{%product}}.id) as products_count',
                ])
                ->where(['parent_id' => $model->parent_id])
                ->leftJoin('{{%product}}', '{{%catalogue}}.id = {{%product}}.catalogue_id')
                ->groupBy('{{%catalogue}}.id')
                ->orderBy(['{{%catalogue}}.id' => SORT_ASC])
                ->all();

            $ids = [$model->id];
        }


        $products = Product::findByCategories($ids)->with('groupProducts')->orderBy(['enduserprice' => SORT_ASC])->all();

        $this->heading = $model->name;
        $this->metaTitle = $this->heading . ' | ' . Yii::t('app', 'Catalogue') . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = Yii::$app->config->siteMetaDescript;
        $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

        $seoInfo = SEOInformation::findModel('catalogue', 'view', $model->id);
        if ( ! is_null($seoInfo))
        {
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

        return $this->render('view', [
            'heading' => $this->heading,
            'model' => $model,
            'categories' => $childCategories,
            'products' => $products,
        ]);
    }

}
