<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class ProductAttachmentXMLReader extends BaseXMLParser
{
    protected $productIndex = -1;
    protected $attachIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->attachIndex = -1;
        $this->productIndex = -1;
        parent::clearResult();
    }

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'print/product') === false && mb_strpos($this->path, 'price/product') === false && mb_strpos($this->path, 'product/product') === false) {
                $this->productIndex++;
                $this->attachIndex = -1;
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

    protected function parseProductAttachment()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_attachment') {
            $this->attachIndex++;
        }
    }

    protected function parseImage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'image') {
            if (mb_strpos($this->path, 'product_attachment/image') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_attachment'][$this->attachIndex]['image'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseFile()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'file') {
            if (mb_strpos($this->path, 'product_attachment/file') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_attachment'][$this->attachIndex]['file'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseMeaning()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'meaning') {
            if (mb_strpos($this->path, 'product_attachment/meaning') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_attachment'][$this->attachIndex]['meaning'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseName()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'name') {
            if (mb_strpos($this->path, 'product/product_attachment/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->productIndex]['product_attachment'][$this->attachIndex]['name'] = $this->reader->value;
                }
            }
        }
    }
}