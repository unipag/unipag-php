<?php

class Unipag_Connection extends Unipag_Object
{
    public static function filter($filter=array(), $api_key=null)
    {
        $class = get_class();
        return self::execFilter($class, $filter, $api_key);
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
}
