<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class SlaveProductXMLReader extends BaseXMLParser
{
    protected $productIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->productIndex = -1;
        parent::clearResult();
    }

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'product/product') !== false) {
                $this->productIndex++;
            }
        }
    }

    protected function parseProductId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_id') {
            if (mb_strpos($this->path, 'product/product/product_id') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_id'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseMainProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'main_product') {
            if (mb_strpos($this->path, 'product/product/main_product') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['main_product'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseCode()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'code') {
            if (mb_strpos($this->path, 'product/product/code') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['code'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseName()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'name') {
            if (mb_strpos($this->path, 'product/product/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['name'] = $this->reader->value;
                }
            } elseif (mb_strpos($this->path, 'product/product/price/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['price']['name'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseSizeCode()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'size_code') {
            if (mb_strpos($this->path, 'product/product/size_code') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['size_code'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseWeight()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'weight') {
            if (mb_strpos($this->path, 'product/product/weight') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['weight'] = $this->reader->value;
                }
            }
        }
    }

    protected function parsePrice()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'price') {
            if (mb_strpos($this->path, 'product/product/price/price') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['price']['price'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseCurrency()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'currency') {
            if (mb_strpos($this->path, 'product/product/price/currency') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['price']['currency'] = $this->reader->value;
                }
            }
        }
    }
}