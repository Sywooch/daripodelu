<?php

namespace rkdev\giftsruxml;

use rkdev\xmlreader\BaseXMLParser;
use \XMLReader;

class FilterXMLReader extends BaseXMLParser
{
    protected $filterTypeIndex = -1;
    protected $filterIndex = -1;

    public function clearResult()
    {
        $this->levelReset();
        $this->pathReset();
        $this->filterTypeIndex = -1;
        $this->filterIndex = -1;
        parent::clearResult();
    }

    protected function parseFiltertype()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filtertype') {
            $this->filterTypeIndex++;
            $this->filterIndex = -1;
        }
    }

    protected function parseFiltertypeid()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filtertypeid') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->filterTypeIndex]['filtertypeid'] = $this->reader->value;
            }
        }
    }

    protected function parseFiltertypename()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filtertypename') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->filterTypeIndex]['filtertypename'] = $this->reader->value;
            }
        }
    }

    protected function parseFilter()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filter') {
            $this->filterIndex++;
        }
    }

    protected function parseFilterid()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filterid') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->filterTypeIndex]['filters'][$this->filterIndex]['filterid'] = $this->reader->value;
            }
        }
    }

    protected function parseFiltername()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'filtername') {
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) {
                $this->result[$this->filterTypeIndex]['filters'][$this->filterIndex]['filtername'] = $this->reader->value;
            }
        }
    }
}