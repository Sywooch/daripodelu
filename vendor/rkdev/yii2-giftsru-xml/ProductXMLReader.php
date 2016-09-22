<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class ProductXMLReader extends BaseXMLParser
{
    protected $productCounter = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->productCounter = -1;
        parent::clearResult();
    }

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'print/product') === false && mb_strpos($this->path, 'price/product') === false && mb_strpos($this->path, 'product/product') === false) {
                $this->productCounter++;
            }
        }
    }

    protected function parseProductId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_id') {
            if (mb_strpos($this->path, 'product/product/product_id') === false && mb_strpos($this->path, 'product/product_id') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['product_id'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseCode()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'code') {
            if (mb_strpos($this->path, 'product/product/code') === false && mb_strpos($this->path, 'product/code') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['code'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseGroup()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'group') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['group'] = $this->reader->value;
            }
        }
    }

    protected function parseName()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'name') {
            if (mb_strpos($this->path, 'product/product/name') === false && mb_strpos($this->path, 'product/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['name'] = $this->reader->value;
                }
            } elseif (mb_strpos($this->path, 'price/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['price']['name'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseProductSize()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_size') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['product_size'] = $this->reader->value;
            }
        }
    }

    protected function parseMaterial()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'matherial') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['matherial'] = $this->reader->value;
            }
        }
    }

    protected function parseContent()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'content') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['content'] = $this->reader->value;
            }
        }
    }

    protected function parseBrand()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'brand') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['brand'] = $this->reader->value;
            }
        }
    }

    protected function parseWeight()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'weight') {
            if (mb_strpos($this->path, 'product/product/weight') === false && mb_strpos($this->path, 'product/weight') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['weight'] = $this->reader->value;
                }
            } elseif (mb_strpos($this->path, 'pack/weight') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['weight'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseSmallImage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'small_image') {
            $src = $this->reader->getAttribute('src');
            $this->result[$this->productCounter]['small_image']['src'] = is_null($src) ? null : $src;
        }
    }

    protected function parseBigImage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'big_image') {
            $src = $this->reader->getAttribute('src');
            $this->result[$this->productCounter]['big_image']['src'] = is_null($src) ? null : $src;
        }
    }

    protected function parseSuperBigImage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'super_big_image') {
            $src = $this->reader->getAttribute('src');
            $this->result[$this->productCounter]['super_big_image']['src'] = is_null($src) ? null : $src;
        }
    }

    protected function parseStatus()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'status') {
            $id = $this->reader->getAttribute('id');
            $this->result[$this->productCounter]['status']['id'] = is_null($id) ? null : $id;
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['status']['@value'] = $this->reader->value;
            }
        }
    }

    protected function parseAmount()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'amount') {
            if (mb_strpos($this->path, 'pack/amount') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['amount'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseVolume()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'volume') {
            if (mb_strpos($this->path, 'pack/volume') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['volume'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseSizex()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'sizex') {
            if (mb_strpos($this->path, 'pack/sizex') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['sizex'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseSizey()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'sizey') {
            if (mb_strpos($this->path, 'pack/sizey') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['sizey'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseSizez()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'sizez') {
            if (mb_strpos($this->path, 'pack/sizez') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['pack']['sizez'] = $this->reader->value;
                }
            }
        }
    }

    protected function parsePrice()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'price') {
            if (mb_strpos($this->path, 'price/price') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['price']['price'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseValue()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'value') {
            if (mb_strpos($this->path, 'price/value') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['price']['value'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseCurrency()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'currency') {
            if (mb_strpos($this->path, 'product/product/price/currency') === false && mb_strpos($this->path, 'product/price/currency') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productCounter]['price']['currency'] = $this->reader->value;
                }
            }
        }
    }
}