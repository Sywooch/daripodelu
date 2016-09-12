<?php


namespace rkdev\xmlreader;

use rkdev\xmlreader\AbstractXMLReader;

class BaseXMLParser extends AbstractXMLReader
{
    /**
     * Called for each recognition
     *
     * @param $event
     * @param $callback
     * @return $this
     */
    public function onEvent($event, $callback)
    {
        if ( !isset($this->_eventStack[$event])) {
            $this->_eventStack[$event] = [];
        }
        $this->_eventStack[$event][] = $callback;

        return $this;
    }

    /*
        Выстреливает событие
    */
    public function fireEvent($event, $params = null, $once = false)
    {
        if ($params == null) {
            $params = [];
        }
        $params['context'] = $this;
        if ( !isset($this->_eventStack[$event])) {
            return false;
        }
        $count = count($this->_eventStack[$event]);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                call_user_func_array($this->_eventStack[$event][$i], $params);
                if ($once == true) {
                    array_splice($this->_eventStack[$event], $i, 1);
                }
            }
        }
    }

    /**
     * Streaming parses xml and invokes methods for certain items
     *
     * For example:
     *    For element <product> tries to invoke parseProduct method
     *
     * All parsing methods must be public.
     */
    public function parse()
    {
        $this->reader->read();
        while ($this->reader->read()) {
            if ($this->reader->nodeType == \XMLReader::ELEMENT) {
                $this->path .= '/' . $this->reader->localName;
                $this->level++;
                $fnName = $this->prepareMethodName($this->reader->localName);
                if (method_exists($this, $fnName)) {
                    $lcn = $this->reader->name;
                    // стреляем по началу парсинга блока
                    $this->fireEvent('beforeParseContainer', array('name' => $lcn));
                    // пробежка по детям
                    if ($this->reader->name == $lcn && $this->reader->nodeType != \XMLReader::END_ELEMENT) {
                        // стреляем событие до парсинга элемента
                        $this->fireEvent('beforeParseElement', array('name' => $lcn));
                        // вызываем функцию парсинга
                        $this->{$fnName}();
                        // стреляем событием по названию элемента
                        $this->fireEvent($fnName);
                        // стреляем событием по окончанию парсинга элемента
                        $this->fireEvent('afterParseElement', array('name' => $lcn));
                    } elseif ($this->reader->nodeType == \XMLReader::END_ELEMENT) {
                        // стреляем по окончанию парсинга блока
                        $this->fireEvent('afterParseContainer', array('name' => $lcn));
                    }
                }
            } elseif ($this->reader->nodeType == \XMLReader::END_ELEMENT) {
                $searchPhrase = '/' . $this->reader->localName;
                $pos = mb_strrpos($this->path, $searchPhrase);
                if ($pos !== false) {
                    $this->path = substr_replace($this->path, '', $pos, strlen($searchPhrase));
                }
                $this->level--;
                $this->fireEvent('afterParseContainer', array('name' => $lcn));
            }
        }

        return $this;
    }

    protected function prepareMethodName($localName)
    {
        $words = explode('_', $localName);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }
        $localName = 'parse' . implode('', $words);

        return $localName;
    }
}