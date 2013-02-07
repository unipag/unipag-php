<?php

class Unipag_Utils_Test extends PHPUnit_Framework_TestCase
{
    public function testUrlEncode()
    {
        $this->assertEquals(Unipag_Utils::urlEncode(array()), '');
        $arr = array('key1' => 'value1', 'key2' => 'value2');
        $encoded = Unipag_Utils::urlEncode($arr);
        $this->assertEquals($encoded, 'key1=value1&amp;key2=value2');
    }
}
