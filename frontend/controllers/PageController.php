<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Html;
use app\models\Page;
use frontend\models\FeedbackForm;

class PageController extends \yii\web\Controller {

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($id)
    {
        $feedbackModel = new FeedbackForm();
        $this->getView()->params['feedbackModel'] = $feedbackModel;

        $model = $this->findModel($id);
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => isset($model->meta_description) && !empty($model->meta_description)? $model->meta_description : Yii::$app->config->siteMetaDescript,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => isset($model->meta_keywords) && !empty($model->meta_keywords)? $model->meta_keywords : Yii::$app->config->siteMetaKeywords,
        ]);
        $this->view->title = isset($model->meta_title) && !empty($model->meta_title)? $model->meta_title . ' | ' . Yii::$app->config->siteName : $model->name . ' | ' . Yii::$app->config->siteName;

        return $this->render('view', [
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
        if (($model = Page::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
