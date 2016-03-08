<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\models\SEOInformation;
use frontend\models\FeedbackForm;
use frontend\models\Product;

class ProductController extends \yii\web\Controller
{
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

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

    public function actionView($id)
    {
        $model = Product::find()
            ->with(['catalogue', 'productAttachments', 'productPrints', 'slaveProducts', 'groupProducts'])
            ->andWhere(['id' => $id])->one();

        if ($model === null)
        {
            throw new NotFoundHttpException();
        }

        if (yii::$app->request->isAjax)
        {
            echo $this->renderAjax('_item', ['model' => $model]);
        }
        else
        {
            /* @var $model Product */
            $this->heading = $model->name;
            $this->metaTitle = $this->heading . ' | ' . $model->catalogue->name . ' | ' . Yii::$app->config->siteName;
            $this->metaDescription = Yii::$app->config->siteMetaDescript;
            $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

            $seoInfo = SEOInformation::findModel('product', 'view', $model->id);
            if ( !is_null($seoInfo))
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
            ]);
        }
    }

}
