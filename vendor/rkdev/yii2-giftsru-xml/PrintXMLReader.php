<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class PrintXMLReader extends BaseXMLParser
{
    protected $printIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->printIndex = -1;
        parent::clearResult();
    }

    protected function parsePrint()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'print') {
            if (mb_strpos($this->path, 'product/print') !== false) {
                $this->printIndex++;
            }
        }
    }

    protected function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'product/print/product') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->printIndex]['product'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseName()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'name') {
            if (mb_strpos($this->path, 'product/print/name') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->printIndex]['name'] = $this->reader->value;
                }
            }
        }
    }

    protected function parseDescription()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'description') {
            if (mb_strpos($this->path, 'product/print/description') !== false) {
                $this->reader->read();
                if ($this->reader->nodeType == XMLReader::TEXT) {
                    $this->result[$this->printIndex]['description'] = $this->reader->value;
                }
            }
        }
    }
}