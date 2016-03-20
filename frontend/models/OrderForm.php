<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class OrderForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $fileOne;
    public $fileTwo;

    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'trim'],
            [['name', 'phone', 'email'], 'required'],
            ['email', 'email'],
            ['name', 'string', 'min' => 2],
            ['phone', 'string', 'min' => 5],
            ['phone', 'match', 'pattern' => '/^((8|\+7|\+[0-9]{1,3})[\- ]?)?(\(?\d{2,5}\)?[\- ]?)?[\d\- ]{6,10}$/'],
            [['name', 'phone', 'email'], 'string', 'max' => 255],
            [['fileOne', 'fileTwo'], 'file', 'extensions' => 'cdr, ai, psd, pdf, jpg, jpeg, png, gif, tif, bmp'],
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
}