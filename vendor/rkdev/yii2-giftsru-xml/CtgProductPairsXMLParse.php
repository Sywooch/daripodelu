<?php


namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class CtgProductPairsXMLParse extends BaseXMLParser
{
    protected $pageIndex = [];

    public function parsePage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'page') {
            if (mb_strpos($this->path, 'product/page') === false) {

            }
        }
    }

    public function clearResult()
    {
        $this->levelReset();
        $this->pageIndex = [];
        parent::clearResult();
    }
}