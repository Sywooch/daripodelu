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
use backend\models\Article;
use backend\models\ArticleSearch;
use backend\behaviors\ContentAliasBehavior;
use common\models\SEOInformation;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
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
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tabIndex = 0;
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $seoInfo = SEOInformation::findModel('article', 'index');
        if (is_null($seoInfo))
        {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'article';
            $seoInfo->action_id = 'index';
        }

        if (isset($_POST['saveSEO']))
        {
            if ($seoInfo->load(Yii::$app->request->post()))
            {
                if ($seoInfo->save())
                {
                    Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
                }
                else
                {
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
     * Displays a single Article model.
     * @param string $id
     * @return mixed
     */
    public function actionView()
    {
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' article.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'article',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ($model->load(Yii::$app->request->post()) && isset($_POST['MenuTree']['alias']))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The article added successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];

                if ( !$model->saveAlias())
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of article.'));
                }

                if (isset($_POST['saveArticle']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        }
        else
        {
            $model->published_date = Yii::$app->formatter->asDatetime(time());

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'article',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ((Yii::$app->request->isAjax || Yii::$app->request->isPjax) && isset($_FILES['model_images']))
        {
            $images = UploadedFile::getInstancesByName('model_images');
            foreach ($images as $image)
            {
                if ($model->getBehavior('photo')->saveImage($image))
                {
                    echo Json::encode(['status' => 1, 'message' => Yii::t('app', 'upload_success')]);
                }
                else
                {
                    echo '';
                }
            }
        }
        elseif ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];

                if ( !$model->saveAlias())
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of article.'));
                }

                if (isset($_POST['saveArticle']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        }
        else
        {
            $model->last_update_date = Yii::$app->formatter->asDatetime($model->last_update_date);
            $model->created_date = Yii::$app->formatter->asDatetime($model->created_date);
            $model->published_date = Yii::$app->formatter->asDatetime($model->published_date);

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'article',
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
        if (isset($_POST['ids']))
        {
            $keys = Yii::$app->request->post('ids');
            $articleModels = Article::findAll(['id' => $keys]);
            $rslt = Article::deleteAll(['id' => $keys]);

            if ($rslt > 0)
            {
                foreach ($articleModels as $articleModel)
                {
                    Image::deleteAllFilesOfOwner(
                        $articleModel->getBehavior('photo')->model,
                        $articleModel->id,
                        $articleModel->getBehavior('photo')->getCtgId()
                    );

                    $imageModel = new Image();
                    $imageModel->model = $articleModel->getBehavior('photo')->model;
                    $imageModel->owner_id = $articleModel->id;
                    $imageModel->ctg_id = $articleModel->getBehavior('photo')->getCtgId();

                    $imageModel->deleteFolderIfEmpty();
                }
            }

            if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
            {
                echo helpers\Json::encode(['status' => 'success', 'rslt' => $rslt]);
            }
            else
            {
                return $this->redirect(isset($_POST['returnURL']) ? Yii::$app->request->post('returnURL') : ['index']);
            }
        }
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
