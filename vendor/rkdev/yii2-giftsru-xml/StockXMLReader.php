<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class StockXMLReader extends BaseXMLParser
{
    protected $stockIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->stockIndex = -1;
        parent::clearResult();
    }

    protected function parseStock()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'stock') {
            $this->stockIndex++;
        }
    }

    protected function parseProductId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product_id') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['product_id'] = $this->reader->value;
            }
        }
    }

    protected function parseCode()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'code') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['code'] = $this->reader->value;
            }
        }
    }

    protected function parseAmount()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'amount') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['amount'] = $this->reader->value;
            }
        }
    }

    protected function parseFree()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'free') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['free'] = $this->reader->value;
            }
        }
    }

    protected function parseInwayamount()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'inwayamount') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['inwayamount'] = $this->reader->value;
            }
        }
    }

    protected function parseInwayfree()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'inwayfree') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['inwayfree'] = $this->reader->value;
            }
        }
    }

    protected function parseDealerprice()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'dealerprice') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['dealerprice'] = $this->reader->value;
            }
        }
    }

    protected function parseEnduserprice()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'enduserprice') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->stockIndex]['enduserprice'] = $this->reader->value;
            }
        }
    }
}