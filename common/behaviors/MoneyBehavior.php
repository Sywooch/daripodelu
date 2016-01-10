<?php

namespace common\behaviors;

use common\components\ActiveRecord;
use yii;
use yii\base\Behavior;

class MoneyBehavior extends Behavior
{

    private $moneyAttributes = [];

    /**
     * @return array
     */
    public function getMoneyAttributes()
    {
        return $this->moneyAttributes;
    }

    /**
     * @param array|string $moneyAttributes
     */
    public function setMoneyAttributes($moneyAttributes)
    {
        if (is_string($moneyAttributes))
        {
            $this->moneyAttributes[] = trim($moneyAttributes);
        }
        elseif (is_array($moneyAttributes))
        {
            foreach ($moneyAttributes as $attr)
            {
                $this->moneyAttributes[] = $attr;
            }
        }
        else
        {
            throw new yii\base\InvalidArgumentException('The parameter $moneyAttributes should be a string or an array.');
        }
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'toMoney',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'toMoney',
            ActiveRecord::EVENT_BEFORE_INSERT => 'toCents',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'toCents',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'toCents',
        ];
    }

    public function toCents($events)
    {
        foreach ($this->moneyAttributes as $attr)
        {
            $this->owner->{$attr} = $this->owner->{$attr} * 100;
        }
    }

    public function toMoney($events)
    {
        foreach ($this->moneyAttributes as $attr)
        {
            $this->owner->{$attr} = $this->owner->{$attr} / 100;
        }
    }
}