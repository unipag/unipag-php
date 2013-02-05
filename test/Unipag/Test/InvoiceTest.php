<?php

class Unipag_Test_Invoice extends PHPUnit_Framework_TestCase
{
    public function testSample()
    {
        $invoice = new Unipag_Invoice();
        $this->assertTrue($invoice instanceof Unipag_Object);
    }
}
