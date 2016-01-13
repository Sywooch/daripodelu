<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\Block;
use backend\models\BlockSearch;
use common\models\MenuTree;

/**
 * BlockController implements the CRUD actions for Block model.
 */
class BlockController extends Controller
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
     * Lists all Block models.
     * @return mixed
     */
    public function actionIndex()
    {
        $positions = array_merge(yii::$app->params['positions'], [Block::NO_POS => 'Нет позиции']);
        foreach ($positions as $code => $name)
        {
            $dataProviders[$code] = new ActiveDataProvider([
                'query' => Block::find()->where(['position' => $code]),
                'sort' => [
                    'defaultOrder' => ['weight' => SORT_ASC],
                ],
            ]);
        }

        return $this->render('index', [
//            'searchModel' => $searchModel,
            'dataProviders' => $dataProviders,
            'positions' => $positions,
        ]);
    }

    /**
     * Displays a single Block model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Block model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Block();
        $model->attachMenuTree(new MenuTree());
        $model->addPositions(yii::$app->params['positions']);

        if ($model->load(Yii::$app->request->post()))
        {
            $maxWeight = $model->getMaxWeightForPosition($model->position);
            $model->weight = $maxWeight + 1;
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The block added successfully.'));

                if (isset($_POST['saveBlock']))
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
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Block model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attachMenuTree(new MenuTree());
        $model->addPositions(yii::$app->params['positions']);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The block added successfully.'));

                if (isset($_POST['saveBlock']))
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
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Block model.
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
     * Deletes a set of items in accordance with the ids array
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDeletescope()
    {
        if (isset($_POST['ids']))
        {
            $keys = Yii::$app->request->post('ids');
            $rslt = Block::deleteAll(['id' => $keys]);
            if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
            {
                echo Json::encode(['status' => 'success', 'rslt' => $rslt]);
            }
            else
            {
                return $this->redirect(isset($_POST['returnURL']) ? Yii::$app->request->post('returnURL') : ['index']);
            }
        }
    }

    public function actionOrder()
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
                    $counter += (Block::updateAll(['weight' => $index], ['id' => intval($id)])) ? 1 : 0;
                }
            }

            $status = ($counter > 0) ? 'success' : 'no_updated';
        }

        echo Json::encode(['status' => $status, 'rslt' => $counter]);
    }

    /**
     * Finds the Block model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Block the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Block::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
