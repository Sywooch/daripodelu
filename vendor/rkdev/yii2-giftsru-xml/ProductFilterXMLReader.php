<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class ProductFilterXMLReader extends BaseXMLParser
{
    protected $productIndex = -1;
    protected $filterIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->productIndex = -1;
        $this->filterIndex = -1;
        parent::clearResult();
    }

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'print/product') === false && mb_strpos($this->path, 'price/product') === false && mb_strpos($this->path, 'product/product') === false) {
                $this->productIndex++;
                $this->filterIndex = -1;
            }
        }
    }

    protected function parseProductId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_id') {
            if (mb_strpos($this->path, 'product/product/product_id') === false && mb_strpos($this->path, 'product/product_id') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_id'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseFilter()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filter') {
            $this->filterIndex++;
        }
    }

    protected function parseFiltertypeid()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filtertypeid') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productIndex]['filters'][$this->filterIndex]['filtertypeid'] = $this->reader->value;
            }
        }
    }

    protected function parseFilterid()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filterid') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productIndex]['filters'][$this->filterIndex]['filterid'] = $this->reader->value;
            }
        }
    }
}