<?php

namespace frontend\components\cart;

use yii;
use yii\base\InvalidParamException;
use frontend\components\cart\ShopCartItem;
use frontend\components\cart\ShopCartItemSize;
use frontend\models\Cart;
use frontend\models\Product;

/**
 * This is class of Shop Cart
 *
 * @package frontend\components\cart
 * @property integer $id cart ID
 */
class ShopCart extends yii\base\Component
{
    protected $id = null;
    /* @var $cartModel Cart */
    protected $cartModel = null;

    /* @var $items ShopCartItem[] */
    protected $items = [];

    public function init()
    {
        $cookies = yii::$app->request->cookies;
        if ($cookies->has('cart'))
        {
            $id = (int) $cookies->getValue('cart');

            $this->cartModel = Cart::findOne($id);
            if ( ! is_null($this->cartModel))
            {
                $this->id = $this->cartModel->id;
                $this->fillCartObject(unserialize($this->cartModel->data));
                $this->cartModel->touch('last_use_date');
            }
            else
            {
                $cookies->remove('cart');
                $this->cartModel = new Cart();
            }
        }
        else
        {
            $this->cartModel = new Cart();
        }

        parent::init();
    }

    /**
     * Adds item to cart
     *
     * @param \frontend\components\cart\ShopCartItem $cartItem
     * @return bool Returns TRUE if the adding succeeded
     */
    public function add(ShopCartItem $cartItem)
    {
        $index = null;
        foreach ($this->items as $key => $item)
        {
            if ($item->productId == $cartItem->productId)
            {
                $index = $key;
                break;
            }
        };

        if (is_null($index))
        {
            $this->items[] = $cartItem;
        }
        else
        {
            $itemsSizesCount = count($this->items[$index]->size);
            $cartItemSizesCount = count($cartItem->size);
            for ($i = 0; $i < $cartItemSizesCount; $i++)
            {
                $exist = false;
                for ($j = 0; $j < $itemsSizesCount; $j++)
                {
                    if ($this->items[$index]->size[$j]->sizeCode == $cartItem->size[$i]->sizeCode)
                    {
                        $this->items[$index]->size[$j]->quantity += $cartItem->size[$i]->quantity;
                        $exist = true;
                    }
                }

                if ( ! $exist)
                {
                    $this->items[$index]->setSize($cartItem->size[$i]);
                }
            }
        }

        $this->cartModel->data = serialize($this->toArray());
        if ($this->cartModel->save())
        {
            $this->id = $this->cartModel->id;
            yii::$app->response->cookies->add(new yii\web\Cookie([
                'name' => 'cart',
                'value' => $this->id,
            ]));

            $this->fillCartObject(unserialize($this->cartModel->data));

            return true;
        }
        else
        {
            return false;
        }
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

    /**
     * Converts cart object to array
     *
     * @return array
     */
    protected function toArray()
    {
        $cart = [];
        foreach ($this->items as $item)
        {
            $cartItem = [];
            $cartItem['productId'] = $item->productId;
            foreach ($item->size as $size)
            {
                $cartItem['size'][] = [
                    'sizeId' => $size->sizeId,
                    'sizeCode' => $size->sizeCode,
                    'quantity' => $size->quantity,
                ];
            }
            $cart[] = $cartItem;
        }

        return $cart;
    }

    /**
     * Converts array to cart object
     *
     * @param array $cartAsArray
     */
    protected function fillCartObject($cartAsArray)
    {
        if ( ! is_array($cartAsArray))
        {
            throw new InvalidParamException('Wrong parameter $cartAsArray of method ' . __METHOD__ . ' of class ' . __CLASS__ . '. $cartAsArray must be an array.');
        }

        $this->items = [];
        $productIds = [];
        foreach ($cartAsArray as $item)
        {
            $cartItem = new ShopCartItem();
            $cartItem->productId = $item['productId'];
            foreach ($item['size'] as $sizeItem)
            {
                $cartItem->setSize(new ShopCartItemSize($sizeItem['sizeId'], $sizeItem['sizeCode'], $sizeItem['quantity']));
            }
            $productIds[] = $cartItem->productId;
            $this->items[] = $cartItem;
        }

        /* @var \frontend\models\Product[] $products */
        $products = Product::find()->where(['id' => $productIds])->all();

        $cartItemsCount = $this->getItemsCount();
        foreach ($products as $product)
        {
            for ($i = 0; $i < $cartItemsCount; $i++)
            {
                if ($this->items[$i]->productId == $product->id)
                {
                    $this->items[$i]->cost = (float) $product->enduserprice;
                }
            }
        }
    }

    /**
     * Returns cart item or null, if the item wasn't found
     *
     * @param $productId
     * @return \frontend\components\cart\ShopCartItem|null
     */
    protected function findItemByProductId($productId)
    {
        $cartItem = null;
        foreach ($this->items as $item)
        {
            if ($item->productId == $productId)
            {
                $cartItem = $item;
                break;
            }
        }

        return $cartItem;
    }
}