<?php

namespace rkdev\loadgifts;


/**
 * Abstract class LoadGiftsXML
 * @package rkdev\loadgifts
 */
class LoadGiftsXML {

    private static $instance = null;

    /**
     * @return LoadGiftsXML
     */
    public static function getInstance()
    {
        if(static::$instance === null)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $fileURL
     * @param string $login
     * @param string $password
     * @return bool|\SimpleXMLElement
     * @throws \Exception
     */
    public function get($fileURL, $login = null, $password = null)
    {
        $xml = false;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $fileURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        if ($login !== null && $password !== null)
        {
            curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);
        }

        $output = curl_exec($ch);

        if ($output === FALSE)
        {
            throw new \Exception("Loading error: " . curl_error($ch));
        }

        curl_close($ch);

        $xml = new \SimpleXMLElement($output);

        return $xml;
    }

    private function __clone() {}

    private function __construct() {}
}
