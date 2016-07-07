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
 * @property integer $validityPeriod storage period in seconds of the information about cart
 * @property integer $id cart ID
 * @property ShopCartItem[] $items
 */
class ShopCart extends yii\base\Component
{
    /* @var $validityPeriod int storage period of the information about cart in seconds */
    public $validityPeriod = 0;

    protected $id = null;
    /* @var $cartModel Cart */
    protected $cartModel = null;

    /* @var $items ShopCartItem[] */
    protected $items = [];

    public function init()
    {
        $cookies = yii::$app->request->cookies;
        if ($cookies->has('cart')) {
            $id = (int)$cookies->getValue('cart');

            $this->cartModel = Cart::findOne($id);
            if ( !is_null($this->cartModel)) {
                $this->id = $this->cartModel->id;
                $this->fillCartObject(unserialize($this->cartModel->data));
                $this->cartModel->touch('last_use_date');
                yii::$app->response->cookies->add(new yii\web\Cookie([
                    'name' => 'cart',
                    'value' => $this->id,
                    'expire' => time() + $this->validityPeriod,
                ]));
            } else {
                yii::$app->response->cookies->remove('cart');
                $this->cartModel = new Cart();
            }
        } else {
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
        foreach ($this->items as $key => $item) {
            if ($item->productId == $cartItem->productId) {
                $index = $key;
                break;
            }
        };

        if (is_null($index)) {
            $this->items[] = $cartItem;
        } else {
            $itemsSizesCount = count($this->items[$index]->size);
            $cartItemSizesCount = count($cartItem->size);
            for ($i = 0; $i < $cartItemSizesCount; $i++) {
                $exist = false;
                for ($j = 0; $j < $itemsSizesCount; $j++) {
                    if ($this->items[$index]->size[$j]->sizeCode == $cartItem->size[$i]->sizeCode) {
                        $this->items[$index]->size[$j]->setQuantity($this->items[$index]->size[$j]->quantity + $cartItem->size[$i]->quantity);
                        $exist = true;
                    }
                }

                if ( !$exist) {
                    $this->items[$index]->setSize($cartItem->size[$i]);
                }
            }
        }

        return $this->saveChanges();
    }

    /**
     * Removes size
     *
     * Removes size with code $sizeCode in item with product ID $productId
     *
     * @param int $productId
     * @param string $sizeCode
     * @param bool|true $saveChanges If true, changes saves in DB
     */
    public function removeSize($productId, $sizeCode, $saveChanges = true)
    {
        $itemsCount = count($this->items);
        for ($i = 0; $i < $itemsCount; $i++) {
            if ($this->items[$i]->productId == $productId) {
                $this->items[$i]->removeSize($sizeCode);
                if (count($this->items[$i]->size) == 0) {
                    $this->removeItem($productId, false);
                }
            }
        }

        if ($saveChanges === true) {
            $this->saveChanges();
        }
    }

    /**
     * Removes item
     *
     * Removes item with product ID $productId
     *
     * @param int $productId
     * @param bool|true $saveChanges
     */
    public function removeItem($productId, $saveChanges = true)
    {
        $itemsCount = count($this->items);
        for ($i = 0; $i < $itemsCount; $i++) {
            if ($this->items[$i]->productId == $productId) {
                unset($this->items[$i]);
            }
        }

        if ($saveChanges === true) {
            $this->saveChanges();
        }
    }

    /**
     * Returns total price of all products in the cart
     *
     * @return float|int
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->getTotalPrice();
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
        foreach ($this->items as $item) {
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
     * @return ShopCartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Returns the cart item by product ID
     *
     * @param int $productId
     * @return \frontend\components\cart\ShopCartItem|null
     */
    public function getItemByProductId($productId)
    {
        $result = null;
        $cartItemsCount = count($this->items);
        for ($i = 0; $i < $cartItemsCount; $i++) {
            if ($this->items[$i]->productId == $productId) {
                $result = $this->items[$i];
            }
        }

        return $result;
    }

    /**
     * Saves changes in DB
     *
     * @param bool $validate
     * @return bool
     */
    public function saveChanges($validate = true)
    {
        if ($validate) {
            $this->validate();
        }

        $this->cartModel->data = serialize($this->toArray());
        if ($this->cartModel->save()) {
            $this->id = $this->cartModel->id;
            yii::$app->response->cookies->add(new yii\web\Cookie([
                'name' => 'cart',
                'value' => $this->id,
                'expire' => time() + $this->validityPeriod,
            ]));

            $this->fillCartObject(unserialize($this->cartModel->data));

            return true;
        } else {
            return false;
        }
    }

    /**
     * Clears the shop's cart
     *
     * @return false|int the number of carts deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of carts deleted is 0, even though the deletion execution is successful.
     * @throws \Exception in case delete failed
     */
    public function clear()
    {
        $result = $this->cartModel->delete();
        if ($result) {
            yii::$app->response->cookies->remove('cart');
            $this->removeItems();
        }

        return $result;
    }

    protected function removeItems()
    {
        $this->items = [];
    }

    protected function validate()
    {
        $itemsCount = count($this->items);
        for ($i = 0; $i < $itemsCount; $i++) {
            $sizeCount = count($this->items[$i]->size);
            for ($j = 0; $j < $sizeCount; $j++) {
                if ($this->items[$i]->size[$j]->quantity == 0) {
                    $this->items[$i]->removeSize($this->items[$i]->size[$j]->sizeCode);
                }
            }

            if (count($this->items[$i]->size) == 0) {
                $this->removeItem($this->items[$i]->productId, false);
            }
        }
    }

    /**
     * Converts cart object to array
     *
     * @return array
     */
    protected function toArray()
    {
        $cart = [];
        foreach ($this->items as $item) {
            $cartItem = [];
            $cartItem['productId'] = $item->productId;
            foreach ($item->size as $size) {
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
        if ( !is_array($cartAsArray)) {
            throw new InvalidParamException('Wrong parameter $cartAsArray of method ' . __METHOD__ . ' of class ' . __CLASS__ . '. $cartAsArray must be an array.');
        }

        $this->items = [];
        $productIds = [];
        foreach ($cartAsArray as $item) {
            $cartItem = new ShopCartItem();
            $cartItem->productId = $item['productId'];
            foreach ($item['size'] as $sizeItem) {
                $cartItem->setSize(new ShopCartItemSize($sizeItem['sizeId'], $sizeItem['sizeCode'], $sizeItem['quantity']));
            }
            $productIds[] = $cartItem->productId;
            $this->items[] = $cartItem;
        }

        /* @var \frontend\models\Product[] $products */
        $products = Product::find()->where(['id' => $productIds])->all();

        $cartItemsCount = $this->getItemsCount();
        foreach ($products as $product) {
            for ($i = 0; $i < $cartItemsCount; $i++) {
                if ($this->items[$i]->productId == $product->id) {
                    $this->items[$i]->price = (float)$product->enduserprice;
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
        foreach ($this->items as $item) {
            if ($item->productId == $productId) {
                $cartItem = $item;
                break;
            }
        }

        return $cartItem;
    }
}