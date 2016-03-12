<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Json;
use frontend\components\cart\ShopCartItem;
use frontend\components\cart\ShopCartItemSize;
use frontend\models\Product;

class CartController extends Controller
{
    public function actionAdd()
    {
        $result = ['status' => 'not_success', 'rslt' => null, 'msg' => 'Method is not Post'];
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
                        if (intval($item) > 0)
                        {
                            list($sizeId, $sizeCode) = explode('_', $key, 2);
                            $cartItem->setSize(new ShopCartItemSize($sizeId, $sizeCode, $item));
                        }
                    }
                }
                else
                {
                    if (intval($size) > 0)
                    {
                        $cartItem->setSize(new ShopCartItemSize(0, ShopCartItemSize::DEAFAULT_SIZE, $size));
                    }
                }

                if ($cartItem->getProductsCount() > 0)
                {
                    if (yii::$app->cart->add($cartItem))
                    {
                        $result['status'] = 'success';
                        $result['msg'] = 'Product is successfull added to cart';
                        $result['rslt'] = yii::$app->cart->getTotalCost();
                    }
                    else
                    {
                        $result['msg'] = 'Product is not added to cart';
                    }
                }
                else
                {
                    $result['msg'] = 'There is no products for adding to cart';
                }
            }
            else
            {
                $result['msg'] = 'No product with id';
            }
        }

        if (yii::$app->request->isAjax)
        {
            return Json::encode($result);
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
