<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\Image;
use app\models\News;
use app\models\NewsSearch;
use backend\behaviors\ContentAliasBehavior;
use common\models\SEOInformation;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tabIndex = 0;
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $seoInfo = SEOInformation::findModel('news', 'index');
        if (is_null($seoInfo)) {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'news';
            $seoInfo->action_id = 'index';
        }

        if (isset($_POST['saveSEO'])) {
            if ($seoInfo->load(Yii::$app->request->post())) {
                if ($seoInfo->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
                }

                $tabIndex = 1;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'seoInfo' => $seoInfo,
            'tabIndex' => $tabIndex,
        ]);
    }

    /**
     * Displays a single News model.
     * @param string $id
     * @return mixed
     */
    public function actionView()
    {
        return $this->redirect(['index']);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' news.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'news',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ($model->load(Yii::$app->request->post()) && isset($_POST['MenuTree']['alias'])) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The news added successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];

                if ( !$model->saveAlias()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of news.'));
                }

                if (isset($_POST['saveNews'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        } else {
            $model->published_date = Yii::$app->formatter->asDatetime(time());

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'news',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ((Yii::$app->request->isAjax || Yii::$app->request->isPjax) && isset($_FILES['model_images'])) {
            $images = UploadedFile::getInstancesByName('model_images');
            foreach ($images as $image) {
                if ($model->getBehavior('photo')->saveImage($image)) {
                    echo Json::encode(['status' => 1, 'message' => Yii::t('app', 'upload_success')]);
                } else {
                    echo '';
                }
            }
        } elseif ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];

                if ( !$model->saveAlias()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of news.'));
                }

                if (isset($_POST['saveNews'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        } else {
            $model->last_update_date = Yii::$app->formatter->asDatetime($model->last_update_date);
            $model->created_date = Yii::$app->formatter->asDatetime($model->created_date);
            $model->published_date = Yii::$app->formatter->asDatetime($model->published_date);

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'news',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes a set of items in accordance with the ids array
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDeletescope()
    {
        if (isset($_POST['ids'])) {
            $keys = Yii::$app->request->post('ids');
            $newsModels = News::findAll(['id' => $keys]);
            $rslt = News::deleteAll(['id' => $keys]);

            if ($rslt > 0) {
                foreach ($newsModels as $newsModel) {
                    Image::deleteAllFilesOfOwner(
                        $newsModel->getBehavior('photo')->model,
                        $newsModel->id,
                        $newsModel->getBehavior('photo')->getCtgId()
                    );

                    $imageModel = new Image();
                    $imageModel->model = $newsModel->getBehavior('photo')->model;
                    $imageModel->owner_id = $newsModel->id;
                    $imageModel->ctg_id = $newsModel->getBehavior('photo')->getCtgId();

                    $imageModel->deleteFolderIfEmpty();
                }
            }

            if (Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
                echo helpers\Json::encode(['status' => 'success', 'rslt' => $rslt]);
            } else {
                return $this->redirect(isset($_POST['returnURL']) ? Yii::$app->request->post('returnURL') : ['index']);
            }
        }
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
