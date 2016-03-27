<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use frontend\components\cart\ShopCart;
use frontend\models\Order;
use frontend\models\Product;
use yii\web\UploadedFile;

class OrderForm extends Model
{
    public $name;
    public $phone;
    public $email;
    /* @var $fileOne UploadedFile */
    public $fileOne;
    /* @var $fileTwo UploadedFile */
    public $fileTwo;

    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'trim'],
            [['name', 'phone', 'email'], 'required'],
            ['email', 'email'],
            ['name', 'string', 'min' => 3],
            ['phone', 'string', 'min' => 5],
            ['phone', 'match', 'pattern' => '/^((8|\+7|\+[0-9]{1,3})[\- ]?)?(\(?\d{2,5}\)?[\- ]?)?[\d\- ]{6,10}$/'],
            [['name', 'phone', 'email'], 'string', 'max' => 255],
            [['fileOne', 'fileTwo'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'cdr, ai, psd, pdf, jpg, jpeg, png, gif, tif, bmp'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя Фамилия',
            'phone' => 'Контактный телефон',
            'email' => 'Электронная почта',
        ];
    }

    public function save(ShopCart $cart, $runValidation = true)
    {
        if ($cart->getItemsCount() == 0)
        {
            return null;
        }

        if ($runValidation && ! $this->validate())
        {
            return false;
        }

        $cartAsArray = [];
        foreach ($cart->items as $item)
        {
            $sizes = [];
            foreach ($item->size as $size)
            {
                $sizes[] = [
                    'sizeId' => $size->sizeId,
                    'sizeCode' => $size->sizeCode,
                    'quantity' => $size->quantity,
                ];
            }
            $cartAsArray[] = [
                'productId' => $item->productId,
                'name' => '',
                'image' => '',
                'price' => $item->price,
                'size' => $sizes,
            ];
        }

        $productIds = ArrayHelper::getColumn($cartAsArray, 'productId');
        $products = Product::find()->where(['id' => $productIds])->all();
        /* @var $products \frontend\models\Product[] */

        $cartItemsCount = count($cartAsArray);
        for ($i = 0; $i < $cartItemsCount; $i++)
        {
            foreach ($products as $product)
            {
                if ($product->id == $cartAsArray[$i]['productId'])
                {
                    $cartAsArray[$i]['name'] = $product->name;
                    if (file_exists($product->smallImagePath))
                    {
                        $type = pathinfo($product->smallImagePath, PATHINFO_EXTENSION);
                        $data = file_get_contents($product->smallImagePath);
                        $cartAsArray[$i]['image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            }
        }

        $order = new Order();
        $order->fio = $this->name;
        $order->phone = $this->phone;
        $order->email = $this->email;
        $order->data = serialize($cartAsArray);
        $order->status = Order::STATUS_NEW;
        if ($order->save())
        {
            if ( ! is_null($this->fileOne) && $this->fileOne instanceof UploadedFile)
            {
                $path = $order->getDirPath();
                if ( ! file_exists($path))
                {
                    mkdir($path, 0777, true);
                }
                $this->fileOne->saveAs($path . '/' . $order->id . '_1.' . $this->fileOne->extension);
            }

            if ( ! is_null($this->fileTwo) && $this->fileTwo instanceof UploadedFile)
            {
                $path = $order->getDirPath();
                if ( ! file_exists($path))
                {
                    mkdir($path, 0777, true);
                }
                $this->fileTwo->saveAs($path . '/' . $order->id . '_2.' . $this->fileTwo->extension);
            }

            return true;
        }

        return false;
    }
}