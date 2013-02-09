<?php

class Unipag_Event extends Unipag_Object
{
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
