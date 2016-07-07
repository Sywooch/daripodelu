<?php

namespace frontend\components\cart;

use yii\base\Object;

/**
 * Class ShopCartItemSize
 * @package frontend\components\cart
 * @property integer $sizeId ID размера
 * @property string $sizeCode Символьный код размера
 * @property integer $quantity Количество товаров размера с кодом $sizeCode
 */
class ShopCartItemSize extends Object
{
    const DEAFAULT_SIZE = 'no_size';

    private $sizeId = null;

    private $sizeCode = '';

    private $quantity = 0;

    /**
     * ShopCartItemSize constructor.
     * @param integer $sizeId ID размера
     * @param string $sizeCode Символьный код размера
     * @param integer $quantity Количество товаров размера с кодом $sizeCode
     */
    public function __construct($sizeId, $sizeCode, $quantity)
    {
        $this->setSizeId($sizeId);
        $this->setSizeCode($sizeCode);
        $this->setQuantity($quantity);
    }

    /**
     * @return null
     */
    public function getSizeId()
    {
        return $this->sizeId;
    }

    /**
     * @param null $sizeId
     */
    public function setSizeId($sizeId)
    {
        $this->sizeId = (int)$sizeId;
    }

    /**
     * @return string
     */
    public function getSizeCode()
    {
        return $this->sizeCode;
    }

    /**
     * @param string $sizeCode
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = (string)$sizeCode;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;
    }
}