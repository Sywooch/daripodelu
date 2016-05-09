<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use backend\models\Order;
use backend\models\OrderSearch;
use common\components\rbac\OrderPermissions;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
     * Lists all Order models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionIndex()
    {
        if ( !Yii::$app->user->can(OrderPermissions::INDEX))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable'))
        {
            if ( !Yii::$app->user->can(OrderPermissions::UPDATE))
            {
                throw new ForbiddenHttpException('Access denied');
            }

            $orderId = Yii::$app->request->post('editableKey');
            $model = Order::findOne($orderId);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['Order']);
            $post = ['Order' => $posted];

            if ($model->load($post))
            {
                $model->save();
                $output = '';
                if (isset($posted['status']))
                {
                    $output = Order::getStatusName($model->status);
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;

            return;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        if ( !Yii::$app->user->can(OrderPermissions::UPDATE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveOrder']))
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
            $files = [];

            if ( !is_null($model))
            {
                $path = $model->getDirPath();
                $urlToDir = $model->getDirUrl();
                $files = glob($path . '/' . $model->id . '_*.*');
                $filesCount = count($files);
                for ($i = 0; $i < $filesCount; $i++)
                {
                    $files[$i] = $urlToDir . '/' . basename($files[$i]);
                }
            }

            return $this->render('update', [
                'model' => $model,
                'files' => $files,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if ( !Yii::$app->user->can(OrderPermissions::DELETE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = Order::find()->where(['id' => (int)$id])->andWhere(['or', ['status' => Order::STATUS_CANCELED], ['status' => Order::STATUS_ARCHIVE]])->one();

        if ( !is_null($model))
        {
            $path = $model->getDirPath();
            $modelId = $model->id;
            if ($model->delete())
            {
                $files = glob($path . '/' . $modelId . '_*.*');
                $filesCount = count($files);
                for ($i = 0; $i < $filesCount; $i++)
                {
                    @unlink($files[$i]);
                }
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
