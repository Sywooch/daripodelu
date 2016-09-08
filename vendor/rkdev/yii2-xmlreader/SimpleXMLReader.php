<?php


namespace rkdev\xmlreader;

use rkdev\xmlreader\AbstractXMLReader;
use rkdev\xmlreader\NodeObject;
use \XMLReader;
use \stdClass;

class SimpleXMLReader extends AbstractXMLReader
{
    public function parse()
    {
//        $this->result = $this->xml2assoc($this->reader);
        $this->result = $this->xml2obj($this->reader);

        return $this;
    }

    protected function xml2obj(\XMLReader &$xml)
    {
        $obj = new NodeObject();
        while ($xml->read()) {
            $nodeName = null;
            $attributeName = null;
            if ($xml->nodeType == XMLReader::END_ELEMENT) break;
            if ($xml->nodeType == XMLReader::ELEMENT and !$xml->isEmptyElement) {
                $nodeName = $xml->name;
                if (isset($obj->{$nodeName}) && ! is_array($obj->{$nodeName})) {
                    $tmp = $obj->{$nodeName};
                    $obj->{$nodeName} = [
                        0 => $tmp,
                        1 => new NodeObject(),
                    ];

                    $obj->{$nodeName}[1] = $this->xml2obj($xml);
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[1][$attributeName] = $xml->value;
                        }
                    }
                } elseif (isset($obj->{$nodeName}) && is_array($obj->{$nodeName})) {
                    $n = count($obj->{$nodeName});
                    $obj->{$nodeName}[$n] = $this->xml2obj($xml);
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[$n][$attributeName] = $xml->value;
                        }
                    }
                } else {
                    $obj->{$nodeName} = $this->xml2obj($xml);
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[$attributeName] = $xml->value;
                        }
                    }
                }
            } elseif ($xml->isEmptyElement) {
                $nodeName = $xml->name;
                if (isset($obj->{$nodeName}) && ! is_array($obj->{$nodeName})) {
                    $tmp = $obj->{$nodeName};
                    $obj->{$nodeName} = [
                        0 => $tmp,
                        1 => new NodeObject(),
                    ];

                    $obj->{$nodeName}[1] = '';
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[1][$attributeName] = $xml->value;
                        }
                    }
                } elseif (isset($obj->{$nodeName}) && is_array($obj->{$nodeName})) {
                    $n = count($obj->{$nodeName});
                    $obj->{$nodeName}[$n] = '';
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[$n][$attributeName] = $xml->value;
                        }
                    }
                } else {
                    $obj->{$nodeName} = '';
                    if ($xml->hasAttributes) {
                        while ($xml->moveToNextAttribute()) {
                            $attributeName = $xml->name;
                            $obj->{$nodeName}[$attributeName] = $xml->value;
                        }
                    }
                }
            } elseif ($xml->nodeType == XMLReader::TEXT) {
                $obj = $xml->value;
            }
        }

        return $obj;
    }
}