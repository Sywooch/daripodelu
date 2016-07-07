<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Page;
use app\models\PageSearch;
use backend\behaviors\ContentAliasBehavior;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
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
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Page model.
     * @param string $id
     * @return mixed
     */
    public function actionView()
    {
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Page();
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'page',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The page added successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];
                $model->showInMenu = $_POST['MenuTree']['show_in_menu'];

                if ( !$model->saveAlias()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of page.'));
                }

                if (isset($_POST['savePage'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attachBehavior('alias', [
            'class' => ContentAliasBehavior::className(),
            'controllerId' => 'page',
            'actionId' => 'view',
            'itemIdAttribute' => 'id',
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $timeOffset = \DateTime::createFromFormat('d.m.Y, H:i:s', $model->created_date, new \DateTimeZone(Yii::$app->formatter->timeZone))->getOffset();
            $timeStamp = \DateTime::createFromFormat('d.m.Y, H:i:s', $model->created_date, new \DateTimeZone(Yii::$app->formatter->timeZone))->getTimestamp();
            $model->created_date = date('Y-m-d H:i:s', $timeStamp - $timeOffset);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                $model->alias = $_POST['MenuTree']['alias'];
                $model->parentMenuItemtId = $_POST['MenuTree']['parent_id'];
                $model->show_in_menu = $_POST['MenuTree']['show_in_menu'];

                if ( !$model->saveAlias()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the alias of news.'));
                }

                if (isset($_POST['savePage'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </ strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        } else {
            $model->last_update_date = Yii::$app->formatter->asDatetime($model->last_update_date);
            $model->created_date = Yii::$app->formatter->asDatetime($model->created_date);

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
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
        if (isset($_POST['ids'])) {
            $keys = Yii::$app->request->post('ids');
            $rslt = Page::deleteAll(['id' => $keys]);
            if (Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
                echo helpers\Json::encode(['status' => 'success', 'rslt' => $rslt]);
            } else {
                return $this->redirect(isset($_POST['returnURL']) ? Yii::$app->request->post('returnURL') : ['index']);
            }
        }
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
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
