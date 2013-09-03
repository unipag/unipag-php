<?php

class Unipag_Test_TestCase extends PHPUnit_Framework_TestCase
{
    const PUB_KEY = 'p_EzLtip2wkZusq7MDNBHs3C';
    const SEC_KEY = 'sdEzLtip2wkZusq7MDNBHs3C';

    public function setUp()
    {
        Unipag_Config::$api_key = $this::SEC_KEY;
        Unipag_Config::$api_url = 'https://api.unipag.com/v1';
    }
}
