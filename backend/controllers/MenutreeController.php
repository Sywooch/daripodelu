<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\MenuTreeSearch;
use common\models\MenuTree;
use dosamigos\transliterator\TransliteratorHelper;

/**
 * MenuTreeController implements the CRUD actions for MenuTree model.
 */
class MenutreeController extends Controller {

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
     * Lists all MenuTree models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ( ! Yii::$app->request->get('MenuTreeSearch'))
        {
            return $this->redirect(['index', 'MenuTreeSearch[show_in_menu]' => 1]);
        }

        $searchModel = new MenuTreeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //$routes = MenuTree::makeRoutes();
        $routes = Yii::$app->cache->get(MenuTree::CACHE_KEY_ROUTES);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new MenuTree model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MenuTree(Yii::$app->cache);

        if ($model->load(Yii::$app->request->post()))
        {
            $parent = $model->findOne(['id' => $model->parent_id]);
            if ( ! is_null($parent))
            {
                if ($model->prev_id > 0)
                {
                    $previous = $model->findOne(['id' => $model->prev_id]);
                    $saveResult = $model->insertAfter($previous);
                }
                else
                {
                    $parent = $model->findOne(['id' => $model->parent_id]);
                    $saveResult = $model->prependTo($parent);
                }

                if ($saveResult)
                {
                    Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The menu item added successfully.'));
                }
                else
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
                }

                return $this->redirect(['index']);
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> The parent menu item not found.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
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
     * Updates an existing MenuTree model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attachCache(Yii::$app->cache);

        if ($id != 1)
        {
            if ($model->load(Yii::$app->request->post()))
            {
                if ($model->parent_id === $model->id)
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> Can not move a node when the target node is same.'));
                }
                else
                {
                    if ($model->prev_id > 0)
                    {
                        $previous = $model->findOne(['id' => $model->prev_id]);
                        $saveResult = $model->insertAfter($previous);
                    }
                    else
                    {
                        $parent = $model->findOne(['id' => $model->parent_id]);
                        $saveResult = $model->prependTo($parent);
                    }

                    if ($saveResult)
                    {
                        Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
                    }
                    else
                    {
                        Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
                    }
                }

                if (isset($_POST['saveMenuItem']))
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
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> You can not edit the root node.'));

            return $this->redirect(['index']);
        }
    }

    /**
     * Deletes an existing MenuTree model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id != 1)
        {
            $model = $this->findModel($id);
            $model->attachCache(Yii::$app->cache);

            if ($model->children()->count() > 0)
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> You can not delete the menu item. It has children nodes. At the beginning, you must delete or move children nodes.'));

                return $this->redirect(['index']);
            }


            if ($model->delete())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The menu item deleted successfully.'));
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> You can not delete the root node.'));
        }

        return $this->redirect(['index']);
    }

    public function actionMakealias($phrase)
    {
        $result = Json::encode(['status' => 'not-ajax-request', 'rslt' => null]);;
        if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
        {
            $phrase = preg_replace('/[ьъыЬЪЫ]+/u', '', $phrase);
            $string = trim(mb_strtolower(TransliteratorHelper::process($phrase)));
            $string = preg_replace('/[^\/\\\a-zA-Z0-9=\s—–-]+/u', '', $string);
            $string = preg_replace('/[\/\\\=\s—–-]+/u', '-', $string);

            $result = Json::encode(['status' => 'success', 'rslt' => $string]);
        }

        echo $result;
    }

    public function actionSiblings()
    {
        if (isset($_POST['depdrop_parents']))
        {
            $out = [];
            $parents = $_POST['depdrop_parents'];
            $model = new MenuTree();
            $model->id = 0;
            $model->parent_id = $parents[0];
            $siblings = ArrayHelper::merge([-1 => '--- ' . Yii::t('app', 'At the beginning') . ' ---'], ArrayHelper::map($model->getSiblingItems(), 'id', 'name'));

            foreach ($siblings as $key => $value)
            {
                $out[] = ['id' => $key, 'name' => $value];
            }

            echo Json::encode(['output' => $out, 'selected' => -1]);

            return;
        }

        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Finds the MenuTree model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MenuTree the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MenuTree::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates root node.
     * @param integer $id
     * @return mixed
     */
    /*public function actionCreateroot()
    {
        $model = new MenuTree();
        $model->name = 'Корень сайта';
        $model->alias = '_root';
        $model->controller_id = 'controller';
        $model->action_id = 'action';
        $model->show_in_menu = MenuTree::HIDE_IN_MENU;
        $model->can_be_parent = MenuTree::PARENT_CAN_BE;
        $model->status = MenuTree::STATUS_ACTIVE;

        if ($model->makeRoot())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Сохранено!</strong> Корневой узел создан.'));
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('app', '<strong>Ошибка!</strong> Корневой узел не создан.'));
        }

        return $this->redirect(['index']);
    }*/
}
