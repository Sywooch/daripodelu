<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\AbstractXMLReader;
use \XMLReader;

class ProductXMLReader extends AbstractXMLReader
{
    protected $productCounter = -1;
    protected $filterCounter = -1;

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if ($this->reader->depth == 1) {
                $this->productCounter++;
            } else {
            }
        }
    }

    protected function parseProductId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_id') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['product_id'] = $this->reader->value;
            }
        }
    }

    protected function parseCode()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'code') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['code'] = $this->reader->value;
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
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->productCounter]['name'] = $this->reader->value;
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

    protected function parseSmallImage()
    {
        
    }

    protected function parseBigImage()
    {

    }

    protected function parseSuperBigImage()
    {

    }
}