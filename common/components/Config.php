<?php

namespace common\components;

use yii;
use common\models\Settings;


/**
 * Class Config
 * @package common\components
 *
 * @property string $siteName
 * @property string $siteAdminEmail
 * @property string $siteEmail
 * @property string $siteMetaDescription
 * @property string $siteMetaKeywords
 * @property integer $newsItemsPerPage
 * @property integer $newsItemsPerHome
 * @property integer $articleItemsPerPage
 * @property integer $articleItemsPerHome
 * @property integer $productsPerPage
 * @property string $gateLogin
 * @property string $gatePassword
 * @property string $sitePhone
 * @property string $siteWorkSchedule
 */
class Config extends yii\base\Component
{

    protected $data = [];

    public function init()
    {
        $config = new Settings();
        $items = $config->getParams();

        /* @var $item Settings */
        foreach ($items as $item)
        {
            if ($item->param)
            {
                $this->data[$item->param] = (isset($item->value) && !is_null($item->value) && !empty($item->value)) ? $item->value : $item->default;
            }
        }

        parent::init();
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }
        else
        {
            throw new yii\base\Exception('Undefined parameter ' . $key);
        }
    }

    public function __get($name)
    {
        $key = '';
        $res = array();
        $name = ucfirst($name);
        preg_match_all('/[A-Z][^A-Z]*?/Us', $name, $res, PREG_SET_ORDER);

        foreach ($res as $word)
        {
            $key .= strtoupper($word[0]) . '_';
        }

        $key = rtrim($key, '_');
        try
        {
            $value = $this->get($key);

            return $value;
        }
        catch (yii\base\Exception $e)
        {
            return parent::__get($name);
        }
    }
}