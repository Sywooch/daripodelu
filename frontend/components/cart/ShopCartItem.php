<?php


namespace frontend\components\cart;

use yii\base\Object;
use frontend\components\cart\ShopCartItemSize;

/**
 * This is class of Shop Cart Item
 *
 * @package frontend\components\cart
 * @property integer $productId ID of product
 * @property float $price The product's cost
 * @property \frontend\components\cart\ShopCartItemSize[] $size the list of sizes with quantity
 */
class ShopCartItem extends Object
{
    /* @var int $productId */
    private $productId;

    /* @var float $price */
    private $price = 0;

    /* @var ShopCartItemSize[] $size */
    private $size = [];

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = (int)$productId;
    }

    /**
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns info about size bu size code
     *
     * @param string $sizeCode
     * @return \frontend\components\cart\ShopCartItemSize|null
     */
    public function getSizeByCode($sizeCode)
    {
        $result = null;
        $sizeCount = count($this->size);
        for ($i = 0; $i < $sizeCount; $i++) {
            if ($this->size[$i]->sizeCode == $sizeCode) {
                $result = $this->size[$i];
                break;
            }
        }

        return $result;
    }

    /**
     * @param \frontend\components\cart\ShopCartItemSize $size
     */
    public function setSize(ShopCartItemSize $size)
    {
        if ($size->sizeCode === ShopCartItemSize::DEAFAULT_SIZE) {
            $this->size = [];
            $this->size[] = $size;
        } else {
            $sizeCount = count($this->size);
            for ($i = 0; $i < $sizeCount; $i++) {
                if ($this->size[$i]->sizeCode == $size->sizeCode) {
                    $this->size[$i]->quantity += $size->quantity;
                    $size = null;
                    break;
                }
            }

            if ( !is_null($size)) {
                $this->size[] = $size;
            }
        }
    }

    /**
     * Removes size
     *
     * Removes size with code $sizeCode
     *
     * @param string $sizeCode
     */
    public function removeSize($sizeCode)
    {
        $sizeCount = count($this->size);
        for ($i = 0; $i < $sizeCount; $i++) {
            if ($this->size[$i]->sizeCode == $sizeCode) {
                unset($this->size[$i]);
            }
        }
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;
    }

    /**
     * Returns common quantity of products of the item
     *
     * @return int
     */
    public function getProductsCount()
    {
        $productCount = 0;
        foreach ($this->size as $size) {
            $productCount += $size->quantity;
        }

        return $productCount;
    }

    public function getTotalPrice()
    {
        return $this->getPrice() * $this->getProductsCount();
    }
}