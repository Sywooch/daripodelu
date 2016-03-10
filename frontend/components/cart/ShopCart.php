<?php

namespace frontend\components\cart;

use yii;
use frontend\components\cart\ShopCartItem;
use frontend\models\Cart;

/**
 * This is class of Shop Cart
 *
 * @package frontend\components\cart
 * @property integer $id cart ID
 */
class ShopCart extends yii\base\Component
{
    protected $id = null;

    /* @var $items ShopCartItem[] */
    protected $items = [];

    public function init()
    {
        $cookies = yii::$app->request->cookies;
        if ($cookies->has('cart'))
        {
            $this->id = (int) $cookies->getValue('cart');

            $cart = Cart::find()->andWhere(['id' => $this->id]);
            if (is_null($cart))
            {
                ;
            }
        }

        parent::init();
    }

    /**
     * Adds item to cart
     *
     * @param \frontend\components\cart\ShopCartItem $cartItem
     */
    public function add(ShopCartItem $cartItem)
    {
        ;
    }

    /**
     * Returns total cost of all products in the cart
     *
     * @return float|int
     */
    public function getTotalCost()
    {
        $totalPrice = 0;
        foreach ($this->items as $item)
        {
            $totalPrice += $item->getTotalCost();
        }

        return $totalPrice;
    }

    /**
     * Returns common number of products in the items list
     *
     * @return int
     */
    public function getProductsCount()
    {
        $productCount = 0;
        foreach ($this->items as $item)
        {
            $productCount += $item->getProductsCount();
        }

        return $productCount;
    }

    /**
     * Returns number of items in the cart
     *
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->items);
    }

    /**
     * Returns cart ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}