<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\components\cart\ShopCartItem;
use frontend\components\cart\ShopCartItemSize;
use frontend\models\Product;

class CartController extends Controller
{
    public function actionAdd()
    {
        if ( yii::$app->request->isPost && yii::$app->request->post('size') !== null )
        {
            $size = yii::$app->request->post('size');

            $cartItem = new ShopCartItem();
            $cartItem->productId = (int) yii::$app->request->post('product');

            $product = Product::find()->where(['id' => $cartItem->productId])->one();
            /* @var $product Product */

            if ( ! is_null($product))
            {
                $cartItem->cost = $product->enduserprice;
                if (is_array($size))
                {
                    foreach ($size as $key => $item)
                    {
                        list($sizeId, $sizeCode) = explode('_', $key, 2);
                        $cartItem->setSize(new ShopCartItemSize($sizeId, $sizeCode, $item));
                    }
                }
                else
                {
                    $cartItem->setSize(new ShopCartItemSize(0, ShopCartItemSize::DEAFAULT_SIZE, $size));
                }

                yii::$app->cart->add($cartItem);
            }
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
