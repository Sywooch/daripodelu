<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Catalogue;
use backend\models\Counter;
use backend\models\Filter;
use backend\models\FilterType;
use backend\models\Product;
use backend\models\ProductFilter;
use backend\models\ProductPrint;
use backend\models\PrintKind;
use backend\models\ProductSearch;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $categories = Catalogue::find()->orderBy(['name' => SORT_ASC])->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $tabNumber = 1)
    {
        $tabNumber = (int) $tabNumber;
        $model = Product::find()->with(['productPrints', 'productAttachments', 'productFilters', 'groupProducts', 'slaveProducts'])->where(['id' => $id])->one();

        if (Yii::$app->request->post('hasEditable'))
        {
            $asd = 1;
        }
        elseif ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                $deleteResult = ProductFilter::deleteAll(['product_id' => $model->id]);
                $filterTypePost = Yii::$app->request->post('FilterType', []);
                $productFilterInsertArr = [];
                foreach ($filterTypePost as $filterTypeId => $filter)
                {
                    if (is_array($filter['value']))
                    {
                        foreach ($filter['value'] as $filterId)
                        {
                            $productFilterInsertArr[] = [
                                $model->id,
                                (int) $filterId,
                                (int) $filterTypeId,
                            ];
                        }
                    }
                }

                if (count($productFilterInsertArr) > 0)
                {
                    Yii::$app->db->createCommand()->batchInsert('{{%product_filter}}', ['product_id', 'filter_id', 'type_id'], $productFilterInsertArr)->execute();
                }

                $productPrintCodes = ArrayHelper::getColumn($model->productPrints, 'print_id');
                if (count($model->prints) !== count($model->productPrints) || count(array_diff($productPrintCodes, $model->prints)) > 0)
                {
                    ProductPrint::deleteAll(['product_id' => $model->id]);
                    $productPrintsInsertArr = [];
                    foreach ($model->prints as $print)
                    {
                        $productPrintsInsertArr[] = [
                            $model->id,
                            $print,
                        ];
                    }

                    if (count($productPrintsInsertArr) > 0)
                    {
                        Yii::$app->db->createCommand()->batchInsert('{{%product_print}}', ['product_id', 'print_id'], $productPrintsInsertArr)->execute();
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveProduct']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->id, 'tabNumber' => $tabNumber]);
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
            $products = Product::find()->where(['group_id' => null])->andWhere(['<>', 'id', $model->id])->orderBy(['name' => SORT_ASC])->all();
            $printIds = ArrayHelper::getColumn($model->productPrints, 'print_id');
            $model->prints = $printIds;
            $prints = PrintKind::find()->all();

            $productsInGroups = Product::find()->where(['not', ['group_id' => null]])->groupBy('group_id')->orderBy(['name' => SORT_ASC])->all();

            $filterTypes = FilterType::find()->with('filters')->orderBy(['name' => SORT_ASC])->all();
            foreach ($model->productFilters as $productFilter)
            {
                foreach ($filterTypes as &$filterType)
                {
                    if ($filterType->id == $productFilter->type_id)
                    {
                        $filterType->value[] = $productFilter->filter_id;
                    }
                }
            }

            return $this->render('update', [
                'model' => $model,
                'productsWithoutGroup' => $products,
                'productsInGroups' => $productsInGroups,
                'prints' => $prints,
                'filterTypes' => $filterTypes,
                'tabNumber' => $tabNumber,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
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
     * Leaves the group of products
     * @param integer $id
     * @param integer $tabNumber the number of active tab
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionLeavegroup($id, $tabNumber = 1)
    {
        $tabNumber = (int) $tabNumber;

        $model = $this->findModel($id);
        $model->group_id = null;
        $result = $model->save(true, ['group_id']);

        if ($result)
        {
            Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));;
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
        }

        return $this->redirect(['update', 'id' => $model->id, 'tabNumber' => $tabNumber]);
    }

    /**
     * Creates group for products
     * @param integer $id
     * @param integer $tabNumber the number of active tab
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreategroup($id, $tabNumber = 1)
    {
        $tabNumber = (int) $tabNumber;

        $model = $this->findModel($id);
        $productPost = Yii::$app->request->post('Product', []);
        if (isset($productPost['groupProductIds']))
        {
            $groupProductIds = $productPost['groupProductIds'];
            $groupProductIds = array_merge($groupProductIds, [$model->id]);
            $newGroupId = Counter::getNextNumber(Counter::PRODUCT_GROUP_ID);
            if (Product::updateAll(['group_id' => $newGroupId], ['id' => $groupProductIds]))
            {
                Counter::incrementValue(Counter::PRODUCT_GROUP_ID);
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The group created successfully.'));
            }
            else
            {
                Yii::$app->session->setFlash('info', Yii::t('app', 'The group was not created.'));
            }
        }

        return $this->redirect(['update', 'id' => $model->id, 'tabNumber' => $tabNumber]);
    }

    public function actionJoingroup($id, $tabNumber = 1)
    {
        $tabNumber = (int) $tabNumber;
        $result = false;

        $model = $this->findModel($id);
        $productPost = Yii::$app->request->post('Product', []);
        if (isset($productPost['groupProductIds']))
        {
            $groupId = (int) $productPost['groupProductIds'];
            $model->group_id = $groupId;
            $result = $model->save(true, ['group_id']);
        }

        if ($result)
        {
            Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));;
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
        }

        return $this->redirect(['update', 'id' => $model->id, 'tabNumber' => $tabNumber]);
    }

    public function actionUpdatachname()
    {
        $asd = Yii::$app->request->isPost;
        $out = Json::encode(['output' => '123456', 'message' => '']);

        echo $out;
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return \backend\models\Product Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
