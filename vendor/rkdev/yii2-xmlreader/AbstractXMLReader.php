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
    protected $path;
    protected $level;

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
            $this->levelReset();
            $this->pathReset();
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

    public function close()
    {
        $this->reader->close();
    }

    public function __destruct()
    {
        $this->close();
    }

    protected function levelReset()
    {
        $this->level = -1;
    }

    protected function pathReset()
    {
        $this->path = '';
    }

    abstract public function parse();
}