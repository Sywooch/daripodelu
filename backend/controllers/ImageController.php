<?php

namespace backend\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use common\models\Image;

//TODO-cms Сделать ajax-редактирование фотографий у новости
//TODO-cms Сделать массовое ajax-удаление отмеченных фотографий у новости

class ImageController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $controllerId = $model->model;
        $itemId = $model->owner_id;
        $catId = $model->ctg_id;
        $model->delete();

        if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
        {
            $dataProvider = $model->getDataProvider($controllerId, $itemId, $catId);

            return $this->renderPartial('_gridview', [
                'dataProvider' => $dataProvider
            ]);
        }
        else
        {
            $this->redirect([$controllerId . '/update', 'id' => $itemId]);
        }
    }

    public function actionDeletescope()
    {
    }

    public function actionSetmain($id)
    {
        $model = $this->findModel($id);
        $rslt = $model->setMain();

        /*if ($rslt)
        {
            yii::$app->session->setFlash('image-success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
        }
        else
        {
            Yii::$app->session->setFlash('image-error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
        }*/

        if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
        {
            $dataProvider = $model->getDataProvider($model->model, $model->owner_id, $model->ctg_id);

            return $this->renderAjax('_gridview', [
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
            $this->redirect([$model->model . '/update', 'id' => $model->owner_id]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save(true, ['title', 'description', 'status']))
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
            }

            return $this->renderAjax('_update', ['model' => $model]);
        }
        else
        {
            return $this->renderAjax('_update', ['model' => $model]);
        }
    }

    public function actionSort()
    {
        $counter = 0;
        $status = 'no_data_found';
        if (isset($_POST['sortData']))
        {
            $sortData = Yii::$app->request->post('sortData');
            if (is_array($sortData) && count($sortData) > 0)
            {
                foreach ($sortData as $index => $id)
                {
                    $counter += (Image::updateAll(['weight' => $index], ['id' => intval($id)])) ? 1 : 0;
                }
            }

            $status = ($counter > 0) ? 'success' : 'no_updated';
        }

        echo Json::encode(['status' => $status, 'rslt' => $counter]);
    }

    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
