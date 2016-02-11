<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\News;
use common\models\SEOInformation;

class NewsController extends \yii\web\Controller
{
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action))
        {
            $this->heading = Yii::t('app', 'News');
            $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
            $this->metaDescription = Yii::$app->config->siteMetaDescript;
            $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

            $seoInfo = SEOInformation::findModel('news', 'index');
            if ( ! is_null($seoInfo))
            {
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
        $query = News::find()->where(['status' => News::STATUS_ACTIVE])->orderBy('published_date DESC');
        $countQuery = clone $query;
        $pages = new Pagination([
            'defaultPageSize' => Yii::$app->config->newsItemsPerPage,
            'forcePageParam' => false,
            'pageSize' => Yii::$app->config->newsItemsPerPage,
            'totalCount' => $countQuery->count(),
        ]);
        $news = $query->offset($pages->offset)->limit($pages->limit)->all();

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
            'news' => $news,
            'pages' => $pages,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => isset($model->meta_description) && !empty($model->meta_description)? $model->meta_description : $this->metaDescription,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => isset($model->meta_keywords) && !empty($model->meta_keywords)? $model->meta_keywords : $this->metaKeywords,
        ]);
        $this->view->title = isset($model->meta_title) && !empty($model->meta_title)? $model->meta_title : $model->name . ' | ' . $this->heading . ' | ' . Yii::$app->config->siteName;

        return $this->render('view', [
            'heading' => $this->heading,
            'model' => $model,
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
        if (($model = News::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
