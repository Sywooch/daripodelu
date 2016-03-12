<?php


namespace frontend\components\cart;

use yii\base\Object;
use frontend\components\cart\ShopCartItemSize;

/**
 * This is class of Shop Cart Item
 *
 * @package frontend\components\cart
 * @property integer $productId ID of product
 * @property float $cost The product's cost
 * @property \frontend\components\cart\ShopCartItemSize[] $size the list of sizes with quantity
 */
class ShopCartItem extends Object
{
    /* @var int $productId */
    private $productId;

    /* @var float $cost */
    private $cost = 0;

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
        $this->productId = (int) $productId;
    }

    /**
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param \frontend\components\cart\ShopCartItemSize $size
     */
    public function setSize(ShopCartItemSize $size)
    {
        if ($size->sizeCode === ShopCartItemSize::DEAFAULT_SIZE)
        {
            $this->size = [];
            $this->size[] = $size;
        }
        else
        {
            $sizeCount = count($this->size);
            for ($i = 0; $i < $sizeCount; $i++)
            {
                if ($this->size[$i]->sizeCode == $size->sizeCode)
                {
                    $this->size[$i]->quantity += $size->quantity;
                    $size = null;
                    break;
                }
            }

            if ( ! is_null($size))
            {
                $this->size[] = $size;
            }
        }
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost($cost)
    {
        $this->cost = (float) $cost;
    }

    /**
     * Returns common quantity of products of the item
     *
     * @return int
     */
    public function getProductsCount()
    {
        $productCount = 0;
        foreach($this->size as $size)
        {
            $productCount += $size->quantity;
        }

        return $productCount;
    }

    public function getTotalCost()
    {
        return $this->getCost() * $this->getProductsCount();
    }
}