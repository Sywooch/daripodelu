<?php


namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use rkdev\xmlreader\SimpleXMLReader;
use rkdev\xmlreader\NodeObject;

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
        $xmlReader = new SimpleXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
        $xmlReader->parse();
        $products = $xmlReader->getResult();

        echo '<pre>';
        print_r($products);
        echo '</pre>';

        return ;
    }
}