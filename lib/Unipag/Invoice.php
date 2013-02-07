<?php

class Unipag_Invoice extends Unipag_Object
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
}
