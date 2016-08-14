<?php


namespace rkdev\xmlreader;

use rkdev\xmlreader\AbstractXMLReader;

class SimpleXMLReader extends AbstractXMLReader
{
    public function parse(&$xml)
    {
        $assoc = NULL;
        $n = 0;
        while ($this->reader->read()) {
            if ($this->reader->nodeType == \XMLReader::END_ELEMENT) break;
            if ($this->reader->nodeType == \XMLReader::ELEMENT and !$this->reader->isEmptyElement) {
                $assoc[$n]['name'] = $this->reader->name;
                if ($this->reader->hasAttributes) while ($this->reader->moveToNextAttribute()) {
                    $assoc[$n]['atr'][$this->reader->name] = $this->reader->value;
                }
                $assoc[$n]['val'] = $this->xml2assoc($this->reader);
                $n++;
            } else if ($this->reader->isEmptyElement) {
                $assoc[$n]['name'] = $this->reader->name;
                if ($this->reader->hasAttributes) while ($this->reader->moveToNextAttribute()) {
                    $assoc[$n]['atr'][$this->reader->name] = $this->reader->value;
                }
                $assoc[$n]['val'] = "";
                $n++;
            } else if ($this->reader->nodeType == \XMLReader::TEXT) $assoc = $this->reader->value;
        }
        $this->result = $assoc;

        return $this;
    }
}