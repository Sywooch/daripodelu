<?php

namespace frontend\controllers;

use frontend\models\ProductFilter;
use yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\SEOInformation;
use frontend\models\Catalogue;
use frontend\models\FeedbackForm;
use frontend\models\FilterType;
use frontend\models\Product;

class CatalogueController extends \yii\web\Controller
{
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action))
        {
            $feedbackModel = new FeedbackForm();
            $this->getView()->params['feedbackModel'] = $feedbackModel;

            return true;
        }

        return false;
    }

    public function actionIndex()
    {
        $categories = Catalogue::find()
            ->with('photo')
            ->where(['parent_id' => 1])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $this->heading = Yii::t('app', 'Catalogue');
        $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = Yii::$app->config->siteMetaDescript;
        $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

        $seoInfo = SEOInformation::findModel('catalogue', 'index');
        if ( ! is_null($seoInfo))
        {
            $this->heading = ($seoInfo->heading == '') ? $this->heading : $seoInfo->heading;
            $this->metaTitle = ($seoInfo->meta_title == '') ? $this->metaTitle : $seoInfo->meta_title;
            $this->metaDescription = ($seoInfo->meta_description == '') ? $this->metaDescription : $seoInfo->meta_description;
            $this->metaKeywords = ($seoInfo->meta_keywords == '') ? $this->metaKeywords : $seoInfo->meta_keywords;
        }

        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $this->metaDescription,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $this->metaKeywords,
        ]);
        $this->view->title = $this->metaTitle;

        return $this->render('index', [
            'heading' => $this->heading,
            'categories' => $categories,
        ]);
    }

    public function actionView($uri, $filterParams = '')
    {
        $model = Catalogue::findOne(['uri' => $uri]);

        if (is_null($model))
        {
            throw new NotFoundHttpException();
        }

        $childCategoriesCount = (int) Catalogue::find()->where(['parent_id' => $model->id])->count();
        $childCategories = $this->getChildCategories($model, $childCategoriesCount);
        if ($childCategoriesCount > 0)
        {
            $ids = ArrayHelper::getColumn($childCategories, 'id');
        }
        else
        {
            $ids = [$model->id];
        }


        //Get the list of products with list of group products----------------------------------------------------------
        if (! empty($filterParams))
        {
            $productsQuery = $this->prepareFilterQuery(Product::find(), $filterParams);
            $productsQueryPart = Product::find()
                ->from(['q' => $productsQuery])
                ->andWhere(['in', 'catalogue_id', $ids])
                ->andWhere(['not', ['group_id' => null]])
                ->groupBy(['group_id']);

            $productsQuery = Product::find()
                ->from(['q' => $productsQuery])
                ->union($productsQueryPart, true)
                ->andWhere(['in', 'catalogue_id', $ids])
                ->andWhere(['group_id' => null]);

            $productsQuery = Product::find()
                ->select('*')
                ->from(['a' => $productsQuery]);
        }
        else
        {
            $productsQuery = Product::findByCategories($ids)->with(['groupProducts']);
        }

        $productProvider = new ActiveDataProvider([
            'query' => $productsQuery,
            'pagination' => [
                'defaultPageSize' => yii::$app->config->productsPerPage,
                'forcePageParam' => false,
                'pageSize' => yii::$app->config->productsPerPage,
            ],
            'sort' => [
                'defaultOrder' => [
                    'enduserprice' => SORT_ASC,
                ]
            ],
        ]);
        //END Get the list of products with list of group products------------------------------------------------------


        //Get new products count
        $newProductsCount = Product::find()->andWhere(['catalogue_id' => $ids])->andWhere(['status_id' => 0])->count();


        //Prepare filters for the list of products----------------------------------------------------------------------
        $productIdsQuery = Product::find()->select(['id'])->andWhere(['catalogue_id' => $ids]);
        $productIdsQuery = $this->prepareFilterQuery($productIdsQuery, $filterParams);
        $productFilters = ProductFilter::find()
            ->select(['type_id', 'filter_id'])
            ->andWhere(['in', 'product_id', $productIdsQuery])
            ->groupBy(['type_id', 'filter_id'])
            ->all();

        $productFilterTypes =[];
        $productFilterIds =[];
        $filtersArr = [];
        foreach ($productFilters as $productFilter)
        {
            /* @var $productFilter \frontend\models\ProductFilter */
            $productFilterTypes[] = $productFilter->type_id;
            $productFilterIds[] = $productFilter->filter_id;
            $filtersArr[$productFilter->type_id][] = $productFilter->filter_id;
        }

        $productFilterTypes = array_unique($productFilterTypes);

        $filters = FilterType::find()
            ->joinWith([
                'filters' => function ($query) use ($filtersArr) {
                    $orCondition = ['or'];
                    foreach ($filtersArr as $filterType => $filterIds)
                    {
                        $orCondition[] = ['and', ['{{%filter}}.type_id' => $filterType], ['{{%filter}}.id' => $filterIds]];
                    }
                    $query->andWhere($orCondition)->orderBy(['{{%filter}}.name' => SORT_ASC]);
                }
            ])
            ->andWhere(['{{%filter_type}}.id' => $productFilterTypes])
            ->orderBy(['{{%filter_type}}.name' => SORT_ASC])
            ->all();
        //END Prepare filters-------------------------------------------------------------------------------------------


        $this->heading = $model->name;
        $this->metaTitle = $this->heading . ' | ' . Yii::t('app', 'Catalogue') . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = Yii::$app->config->siteMetaDescript;
        $this->metaKeywords = Yii::$app->config->siteMetaKeywords;

        $seoInfo = SEOInformation::findModel('catalogue', 'view', $model->id);
        if ( ! is_null($seoInfo))
        {
            $this->heading = ($seoInfo->heading == '') ? $this->heading : $seoInfo->heading;
            $this->metaTitle = ($seoInfo->meta_title == '') ? $this->metaTitle : $seoInfo->meta_title;
            $this->metaDescription = ($seoInfo->meta_description == '') ? $this->metaDescription : $seoInfo->meta_description;
            $this->metaKeywords = ($seoInfo->meta_keywords == '') ? $this->metaKeywords : $seoInfo->meta_keywords;
        }

        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $this->metaDescription,
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $this->metaKeywords,
        ]);
        $this->view->title = $this->metaTitle;

        return $this->render('view', [
            'heading' => $this->heading,
            'uri' => $uri,
            'filterParams' => ($filterParams == '')? [] : $this->parseFilterStringQuery($filterParams),
            'model' => $model,
            'categories' => $childCategories,
            'productsProvider' => $productProvider,
            'newProductsCount' => $newProductsCount,
            'filterTypes' => $filters
        ]);
    }

    /**
     * @param yii\db\ActiveQuery $query
     * @param $filterParams string. For example, 8.32, 10.423
     * @throw yii\base\InvalidParamException
     * @return yii\db\ActiveQuery
     */
    protected function prepareFilterQuery(yii\db\ActiveQuery $query, $filterParams)
    {
        if (trim($filterParams) == '')
        {
            return $query;
        }

        if (! preg_match('/^[\d]+\.[\d]+(\-[\d]+\.[\d]+)*$/', $filterParams))
        {
            throw new InvalidParamException('Wrong parameter $filterParams of method ' . __METHOD__ . ' of class ' . __CLASS__ . '.');
        }

        $filters = $this->parseFilterStringQuery($filterParams);
        $productFiltersQuery = ProductFilter::find()->select('product_id');
        $existQueryToProductFilter = false;
        $counter = 0;
        foreach($filters as $filterType => $filter)
        {
            if ($filterType == 8)
            {
                if ($filter == 229)
                {
                    $query->andWhere(['status_id' => 0]);
                }
            }
            else
            {
                $existQueryToProductFilter = true;
                $counter++;
                $productFiltersQuery->orWhere(['and', 'type_id=' . $filterType, 'filter_id=' . $filter]);
            }
        }

        if ($existQueryToProductFilter === true)
        {
            $productFilter = $productFiltersQuery->groupBy('product_id')->having(['>', 'COUNT(product_id)', $counter-1])->all();
            $productIds = ArrayHelper::getColumn($productFilter, 'product_id');
            $query->andWhere(['id' => $productIds]);
        }


        return $query;
    }

    /**
     * @param $filterParams string
     * @throw yii\base\InvalidParamException
     * @return array the list of filters, where the key is type of filter
     */
    protected function parseFilterStringQuery($filterParams)
    {
        $filters = [];
        if (trim($filterParams) == '')
        {
            return $filters;
        }

        if (! preg_match('/^[\d]+\.[\d]+(\-[\d]+\.[\d]+)*$/', $filterParams))
        {
            throw new InvalidParamException('Wrong parameter $filterParams of method ' . __METHOD__ . ' of class ' . __CLASS__ . '.');
        }

        $filterPairs = explode('-', $filterParams);
        foreach($filterPairs as $filterPair)
        {
            list($filterType, $filter) = explode('.', $filterPair);
            $filterType = intval($filterType);
            $filter = intval($filter);
            $filters[$filterType] = $filter;
        }

        return $filters;
    }

    /**
     * @param Catalogue $model
     * @param $childCategoriesCount integer
     * @return array frontend\models\Catalogue[]
     */
    protected function getChildCategories(Catalogue $model, $childCategoriesCount)
    {
        //Get child categories
        if ($childCategoriesCount > 0)
        {
            $childCategories = Catalogue::find()
                ->select([
                    '{{%catalogue}}.*',
                    'COUNT({{%product}}.id) as products_count',
                ])
                ->where(['parent_id' => $model->id])
                ->leftJoin('{{%product}}', '{{%catalogue}}.id = {{%product}}.catalogue_id')
                ->groupBy('{{%catalogue}}.id')
                ->orderBy(['{{%catalogue}}.id' => SORT_ASC])
                ->all();
        }
        else
        {
            $childCategories = Catalogue::find()
                ->select([
                    '{{%catalogue}}.*',
                    'COUNT({{%product}}.id) as products_count',
                ])
                ->where(['parent_id' => $model->parent_id])
                ->leftJoin('{{%product}}', '{{%catalogue}}.id = {{%product}}.catalogue_id')
                ->groupBy('{{%catalogue}}.id')
                ->orderBy(['{{%catalogue}}.id' => SORT_ASC])
                ->all();
        }

        return $childCategories;
    }
}
