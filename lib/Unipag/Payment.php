<?php

class Unipag_Payment extends Unipag_Object
{
    public function __construct($params=array(), $api_key=null)
    {
        parent::__construct($params, $api_key);
        if (!array_key_exists('currency', $params)) {
            $default_currency = Unipag_Config::$currency;
            if ($default_currency) {
                $this->currency = $default_currency;
            }
        }
    }

    public static function get($id, $api_key=null)
    {
        $class = get_class();
        return self::execGet($class, $id, $api_key);
    }

    public function reload()
    {
        return $this->execReload();
    }

    public static function create($params, $api_key=null)
    {
        $class = get_class();
        return self::execCreate($class, $params, $api_key);
    }

    public function cancel()
    {
        return $this->execRemove();
    }

    public static function filter($filter=array(), $api_key=null)
    {
        $class = get_class();
        return self::execFilter($class, $filter, $api_key);
    }
}
