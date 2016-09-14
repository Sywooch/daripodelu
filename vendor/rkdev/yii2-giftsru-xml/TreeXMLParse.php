<?php


namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class TreeXMLParse extends BaseXMLParser
{
    protected $pageIndex = [];

    protected function parsePage()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'page') {
            if (mb_strpos($this->path, 'product/page') === false) {
                $level = $this->level + 1;
                if (isset($this->pageIndex[$level])) {
                    $this->pageIndex[$level] = $this->pageIndex[$level] + 1;
                } else {
                    $this->pageIndex[$level] = 0;
                }
                $index = $this->pageIndex[$level];
                $parentPageId = $this->reader->getAttribute('parent_page_id');
                $this->result[$level . '_' . $index]['parent_page_id'] = (! is_null($parentPageId)) ? $parentPageId : 0;
            }
        }
    }

    protected function parsePageId()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'page_id') {
            $index = $this->pageIndex[$this->level];
            $this->reader->read();
            if($this->reader->nodeType == XMLREADER::TEXT) {
                $this->result[$this->level . '_' . $index]['page_id'] = $this->reader->value;
            }
        }
    }

    protected function parseName()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'name') {
            $index = $this->pageIndex[$this->level];
            $this->reader->read();
            if($this->reader->nodeType == XMLREADER::TEXT) {
                $this->result[$this->level . '_' . $index]['name'] = $this->reader->value;
            }
        }
    }

    protected function parseUri()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'uri') {
            $index = $this->pageIndex[$this->level];
            $this->reader->read();
            if($this->reader->nodeType == XMLREADER::TEXT) {
                $this->result[$this->level . '_' . $index]['uri'] = $this->reader->value;
            }
        }
    }

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->pageIndex = [];
        parent::clearResult();
    }
}