<?php

class Unipag_Object
{
    private $keys;

    public function __construct($params=array(), $api_key=null)
    {
        if (!is_array($params)) {
            throw new Unipag_Exception(
                'You must pass an array as a first agrument for constructor'
            );
        }
        $this->api_key = $api_key;
        foreach ($params as $k => $v) {
            $this->__set($k, $v);
        }
    }

    public function __get($name)
    {
        return $this->keys[$name];
    }

    public function __set($name, $value)
    {
        if (is_array($value)) {
            return $this->keys[$name] = self::fromArray($value, $this->api_key);
        } else {
            return $this->keys[$name] = $value;
        }
    }

    public static function fromJson($json_str)
    {
        return self::fromArray(json_decode($json_str, true));
    }

    public static function fromArray($arr, $api_key=null)
    {
        $obj_name = null;
        if ($arr && array_key_exists('object', $arr)) {
            $obj_name = ucfirst(strtolower($arr['object']));
        }
        # I would prefer not to hardcode class names and just catch an attempt
        # of wrong name class creation, but it causes PHP Fatal Error.
        # People on Stackoverflow say that I shouldn't try to recover after it.
        $child_classes = array(
            'Invoice',
            'Payment',
            'Event',
            'Connection',
        );
        if ($obj_name && in_array($obj_name, $child_classes)) {
            $class = 'Unipag_'.$obj_name;
            unset($arr['object']);
            return new $class($arr, $api_key);

        }
        $drill_down = array();
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $drill_down[$k] = self::fromArray($v);
            } else {
                $drill_down[$k] = $v;
            }

        }
        return $drill_down;
    }
}
