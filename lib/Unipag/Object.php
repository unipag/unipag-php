<?php

class Unipag_Object
{
    private $params;
    public function __construct($params=array(), $api_key=null)
    {
        if (!is_array($params)) {
            throw new Unipag_Exception(
                'You must pass an array as a first agrument for constructor'
            );
        }
        $this->params = $params;
        $this->api_key = $api_key;
    }

    public function __get($name) {
        return $this->params[$name];
    }

    public function __set($name, $value) {
        return $this->params[$name] = $value;
    }
}
