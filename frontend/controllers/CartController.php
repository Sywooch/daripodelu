<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Json;
use frontend\components\cart\ShopCartItem;
use frontend\components\cart\ShopCartItemSize;
use frontend\models\OrderForm;
use frontend\models\Product;

class CartController extends Controller
{
    public $layout = 'main-3.php';
    private $heading;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;

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
                $cartItem->price = $product->enduserprice;
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
                        $result['rslt'] = yii::$app->cart->getTotalPrice();
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
        $orderForm = new OrderForm();
        $cart = yii::$app->cart;
        $productIds = [];
        foreach ($cart->items as $cartItem)
        {
            $productIds[] = $cartItem->productId;
        }

        $products = Product::find()->where(['id' => $productIds])->all();

        $this->heading = yii::t('app', 'Cart');
        $this->metaTitle = $this->heading . ' | ' . Yii::$app->config->siteName;
        $this->metaDescription = yii::$app->config->siteMetaDescript;
        $this->metaKeywords = yii::$app->config->siteMetaKeywords;

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
            'products' => $products,
            'cart' => $cart,
            'orderForm' => $orderForm,
        ]);
    }

    public function actionDeletesize($productId, $sizeCode)
    {
        yii::$app->cart->removeSize($productId, $sizeCode);

        return $this->actionIndex();
    }

    public function actionChangequantity()
    {
        if (yii::$app->request->isPost && yii::$app->request->post('item') !== null)
        {
            $items = yii::$app->request->post('item');
            if (is_array($items))
            {
                foreach ($items as $item)
                {
                    $cartItem = yii::$app->cart->getItemByProductId($item['product']);
                    if ( ! is_null($cartItem))
                    {
                        if (is_array($item['size']))
                        {
                            foreach ($item['size'] as $key => $quantity)
                            {
                                list($sizeId, $sizeCode) = explode('_', $key, 2);
                                $cartItemSize = $cartItem->getSizeByCode($sizeCode);
                                if ( !is_null($cartItemSize) && $cartItemSize->quantity != $quantity)
                                {
                                    $cartItemSize->quantity = (int) $quantity;
                                }
                            }
                        }
                    }
                }
            }
        }

        yii::$app->cart->saveChanges();

        return $this->actionIndex();
    }
}
