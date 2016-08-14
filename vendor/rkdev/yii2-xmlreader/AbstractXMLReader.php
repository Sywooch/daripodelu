<?php

namespace rkdev\xmlreader;

/**
 * Class wrapper for XMLReader class
 *
 * @package rkdev\xmlreader
 */
abstract class AbstractXMLReader
{
    /* @var $reader \XMLReader */
    protected $reader;
    protected $result = [];
    protected $_eventStack = [];
    protected $path = '';

    /**
     * AbstractXMLReader constructor.
     * Creates XMLReader instant and loads xml
     *
     * @param $xml_path
     */
    public function __construct($xml_path)
    {
        $this->reader = new \XMLReader();
        if (is_file($xml_path)) {
            $this->reader->open($xml_path);
        } else {
            throw new \Exception('XML file {' . $xml_path . '} not exists!');
        }
    }

    /**
     * Returns parsing results
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Clears results
     */
    public function clearResult()
    {
        $this->result = [];
    }
    
    abstract public function parse();
}