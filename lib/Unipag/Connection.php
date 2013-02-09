<?php

class Unipag_Connection extends Unipag_Object
{
    public static function filter($filter=array(), $api_key=null)
    {
        $class = get_class();
        return self::execFilter($class, $filter, $api_key);
    }
}
