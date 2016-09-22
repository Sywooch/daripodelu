<?php


namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class CtgProductPairsXMLParse extends BaseXMLParser
{
    protected $pairIndex = -1;

    public function parseProduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'product') {
            if (mb_strpos($this->path, 'product/product') === false) {
                $this->pairIndex++;
            } else {
                $this->reader->read();
                if($this->reader->nodeType == XMLREADER::TEXT) {
                    $this->result[$this->pairIndex]['product'] = $this->reader->value;
                }
            }
        }
    }

    public function parsePage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'page') {
            if (mb_strpos($this->path, 'product/page') !== false) {
                $this->reader->read();
                if($this->reader->nodeType == XMLREADER::TEXT) {
                    $this->result[$this->pairIndex]['page'] = $this->reader->value;
                }
            }
        }
    }

    public function clearResult()
    {
        $this->levelReset();
        $this->pairIndex = -1;
        parent::clearResult();
    }
}