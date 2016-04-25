<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\Counter;
use backend\models\Product;
use backend\models\SlaveProduct;
use backend\models\SlaveProductSearch;

/**
 * SlaveproductController implements the CRUD actions for SlaveProduct model.
 */
class SlaveproductController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SlaveProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SlaveProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $products = Product::find()->orderBy(['name' => SORT_ASC])->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => $products,
        ]);
    }

    /**
     * Creates a new SlaveProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SlaveProduct();
        $model->scenario = SlaveProduct::SCENARIO_INSERT;

        if ($model->load(Yii::$app->request->post()))
        {
            $model->id = Counter::getNextNumber('slave_product_id');
            $model->user_row = SlaveProduct::IS_USER_ROW;

            if ($model->save())
            {
                Counter::incrementValue('slave_product_id');
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveSlave']))
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
            $products = Product::find()->orderBy(['name' => SORT_ASC])->all();

            return $this->render('create', [
                'model' => $model,
                'products' => $products,
            ]);
        }
    }

    /**
     * Updates an existing SlaveProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveSlave']))
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
            $products = Product::find()->orderBy(['name' => SORT_ASC])->all();

            return $this->render('update', [
                'model' => $model,
                'products' => $products,
            ]);
        }
    }

    /**
     * Deletes an existing SlaveProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SlaveProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SlaveProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SlaveProduct::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
