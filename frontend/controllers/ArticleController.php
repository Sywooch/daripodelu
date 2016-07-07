<?php

namespace frontend\controllers;

use yii;
use yii\data\Pagination;
use yii\helpers\Html;
use common\models\SEOInformation;
use frontend\models\FeedbackForm;
use frontend\models\Article;

class ArticleController extends \yii\web\Controller
{
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $feedbackModel = new FeedbackForm();
            $this->getView()->params['feedbackModel'] = $feedbackModel;

            $this->heading = Yii::t('app', 'Articles');
            $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
            $this->metaDescription = Yii::$app->config->siteMetaDescript;
            $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

            $seoInfo = SEOInformation::findModel('article', 'index');
            if ( !is_null($seoInfo)) {
                $this->heading = ($seoInfo->heading == '') ? $this->heading : $seoInfo->heading;
                $this->metaTitle = ($seoInfo->meta_title == '') ? $this->heading . ' | ' . Yii::$app->config->siteName : $seoInfo->meta_title;
                $this->metaDescription = ($seoInfo->meta_description == '') ? $this->metaDescription : $seoInfo->meta_description;
                $this->metaKeywords = ($seoInfo->meta_keywords == '') ? $this->metaKeywords : $seoInfo->meta_keywords;
            }

            return true;
        }

        return false;
    }

    public function actionIndex()
    {
        $query = Article::find()->with('mainPhoto')->where(['status' => Article::STATUS_ACTIVE])->orderBy('published_date DESC');
        $countQuery = clone $query;
        $pages = new Pagination([
            'defaultPageSize' => Yii::$app->config->articleItemsPerPage,
            'forcePageParam' => false,
            'pageSize' => Yii::$app->config->articleItemsPerPage,
            'totalCount' => $countQuery->count(),
        ]);
        $articles = $query->offset($pages->offset)->limit($pages->limit)->all();

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
            'articles' => $articles,
            'pages' => $pages,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $lastArticles = Article::find()->with('mainPhoto')->where(['status' => Article::STATUS_ACTIVE])->andWhere(['not', ['id' => $id]])->orderBy('published_date DESC')->limit(4)->all();

        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => isset($model->meta_description) && !empty($model->meta_description) ? $model->meta_description : $this->metaDescription,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => isset($model->meta_keywords) && !empty($model->meta_keywords) ? $model->meta_keywords : $this->metaKeywords,
        ]);
        $this->view->title = isset($model->meta_title) && !empty($model->meta_title) ? $model->meta_title : $model->name . ' | ' . $this->heading . ' | ' . Yii::$app->config->siteName;

        return $this->render('view', [
            'heading' => $this->heading,
            'model' => $model,
            'lastArticles' => $lastArticles,
        ]);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
