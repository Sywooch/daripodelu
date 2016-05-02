<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\Image;
use backend\models\Catalogue;
use backend\models\CatalogueSearch;
use backend\models\Counter;
use backend\models\Product;
use backend\models\ProductSearch;
use common\models\SEOInformation;

/**
 * CatalogueController implements the CRUD actions for Catalogue model.
 */
class CatalogueController extends Controller
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
     * Lists all Catalogue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tabIndex = 0;
        $searchModel = new CatalogueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Catalogue::find()->with('photo')->where(['parent_id' => 1]));

        $seoInfo = SEOInformation::findModel('catalogue', 'index');
        if (is_null($seoInfo))
        {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'catalogue';
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
            'parentId' => 1,
        ]);
    }

    /**
     * Lists all Catalogue models.
     * @return mixed
     */
    public function actionCategory($id)
    {
        $tabIndex = 0;
        $searchModel = new CatalogueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Catalogue::find()->where(['parent_id' => $id]));

        $productSearchModel = new ProductSearch();
        $productDataProvider = $productSearchModel->search(Yii::$app->request->queryParams, Product::find()->where(['catalogue_id' => $id]));

        $seoInfo = SEOInformation::findModel('catalogue', 'view', $id);
        if (is_null($seoInfo))
        {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'catalogue';
            $seoInfo->action_id = 'view';
            $seoInfo->item_id = $id;
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

        return $this->render('category', [
            'category' => Catalogue::findOne(['id' => $id]),
            'searchModel' => $searchModel,
            'productSearchModel' => $productSearchModel,
            'dataProvider' => $dataProvider,
            'productDataProvider' => $productDataProvider,
            'seoInfo' => $seoInfo,
            'tabIndex' => $tabIndex,
            'parentId' => (int) $id,
        ]);
    }

    /**
     * Creates a new Catalogue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = 0)
    {
        $model = new Catalogue();
        $seoInfo = new SEOInformation();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->id = Counter::getNextNumber(Counter::CATALOGUE_ID);
            $model->user_row = Catalogue::IS_USER_ROW;

            if ($model->save())
            {
                Counter::incrementValue(Counter::CATALOGUE_ID);
                if ($seoInfo->load(Yii::$app->request->post()))
                {
                    $seoInfo->controller_id = 'catalogue';
                    $seoInfo->action_id = 'view';
                    $seoInfo->item_id = $model->id;
                    if (isset($_POST['SEOInformation']) && $seoInfo->save())
                    {
                        Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> SEO data saved successfully.'));
                    }
                    else
                    {
                        Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the SEO data.'));
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Category saved successfully.'));
                if (isset($_POST['saveCategory']))
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
            $categories = Catalogue::find()->all();
            $categoriesArr = $this->makeTreeForDroplist($categories);
            $category = null;
            if (intval($id) > 0)
            {
                foreach ($categories as $item)
                {
                    if ($item->id == $id)
                    {
                        $category = $item;
                    }
                }
                $model->parent_id = (int) $id;
            }

            return $this->render('create', [
                'model' => $model,
                'seoInfo' => $seoInfo,
                'categories' => $categoriesArr,
                'category' => $category,
            ]);
        }
    }

    /**
     * Updates an existing Catalogue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $tabIndex = 0;
        $model = $this->findModel($id);
        $seoInfo = SEOInformation::findModel('catalogue', 'view', $model->id);
        if (is_null($seoInfo))
        {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'catalogue';
            $seoInfo->action_id = 'view';
            $seoInfo->item_id = $model->id;
        }

        if (isset($_POST['SEOInformation']) && $seoInfo->load(Yii::$app->request->post()))
        {
            if ($seoInfo->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> SEO data saved successfully.'));
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the SEO data.'));
            }
        }

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

                if (isset($_POST['saveCategory']))
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
            $categories = Catalogue::find()->all();
            $categoriesArr = $this->makeTreeForDroplist($categories);

            return $this->render('update', [
                'model' => $model,
                'seoInfo' => $seoInfo,
                'categories' => $categoriesArr,
                'tabIndex' => $tabIndex,
            ]);
        }
    }

    /**
     * Deletes an existing Catalogue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $id = $model->id;
        if ($model->delete())
        {
            $seoInfo = SEOInformation::findModel('catalogue', 'view', $id);
            if ( !is_null($seoInfo))
            {
                $seoInfo->delete();
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Catalogue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalogue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Catalogue::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param \backend\models\Catalogue[] $categories
     * @param int $parentId
     * @param int $level
     * @return array
     */
    protected function makeTreeForDroplist($categories, $parentId = 0, $level = 0)
    {
        $arr = [];
        foreach ($categories as $category)
        {
            if ($category->parent_id == $parentId)
            {
                $arr[$category->id] = str_repeat('- - ', $level) . $category->name;
//                $arr = array_merge($arr, $this->makeTreeForDroplist($categories, $category->id, $level + 1));
                $arr = $arr + $this->makeTreeForDroplist($categories, $category->id, $level + 1);
            }
        }

        return $arr;
    }
}
