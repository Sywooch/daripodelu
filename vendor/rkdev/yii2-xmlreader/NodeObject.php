<?php

namespace rkdev\xmlreader;

use yii\base\InvalidParamException;

class NodeObject implements \ArrayAccess
{
    protected $value;
    protected $attributes = [];

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        if (! $value instanceof NodeObject) {
            $this->$name = new NodeObject();
            $this->$name->setValue($value);
        } else {
            $this->$name = $value;
        }
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }
}