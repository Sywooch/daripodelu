<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action))
        {
            $referrer = Yii::$app->request->get('referrer', false);
            if ($referrer !== false && trim($referrer) != '')
            {
                if ( ! Url::isRelative($referrer))
                {
                    throw new NotFoundHttpException();
                }
            };

            return true;
        }

        return false;
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
    public function actionCreate($id = 0, $referrer = '')
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
                    return (trim($referrer) == '') ? $this->redirect(['index']): $this->redirect($referrer);
                }
                else
                {
                    return (trim($referrer) == '') ? $this->redirect(['update', 'id' => $model->id]): $this->redirect(['update', 'id' => $model->id, 'referrer' => $referrer]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return (trim($referrer) == '') ? $this->redirect(['index']): $this->redirect($referrer);
            }
        }
        else
        {
            $products = Product::find()->orderBy(['name' => SORT_ASC])->all();
            $model->parent_product_id = (int) $id;

            return $this->render('create', [
                'model' => $model,
                'products' => $products,
                'referrer' => $referrer,
            ]);
        }
    }

    /**
     * Updates an existing SlaveProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $referrer = '')
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveSlave']))
                {
                    return (trim($referrer) == '') ? $this->redirect(['index']): $this->redirect($referrer);
                }
                else
                {
                    return (trim($referrer) == '') ? $this->redirect(['update', 'id' => $model->id]): $this->redirect(['update', 'id' => $model->id, 'referrer' => $referrer]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return (trim($referrer) == '') ? $this->redirect(['index']): $this->redirect($referrer);
            }
        }
        else
        {
            $products = Product::find()->orderBy(['name' => SORT_ASC])->all();

            return $this->render('update', [
                'model' => $model,
                'products' => $products,
                'referrer' => $referrer,
            ]);
        }
    }

    /**
     * Deletes an existing SlaveProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $referrer = '')
    {
        $this->findModel($id)->delete();

        return (trim($referrer) == '') ? $this->redirect(['index']): $this->redirect($referrer);
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
