<?php


namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class XmlController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
//        $products = new \SimpleXMLElement(file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml'));
//        $products = new \SimpleXMLElement(file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml'));
//        $products = new \SimpleXMLElement(file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml'));
//        $products = new \SimpleXMLElement(file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml'));
        $products = new \SimpleXMLElement(file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml'));

        echo '<pre>';
        print_r($products);
        echo '</pre>';

        return ;
    }
}