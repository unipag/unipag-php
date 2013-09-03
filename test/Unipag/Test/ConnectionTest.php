<?php

require_once 'TestCase.php';

class Unipag_Test_ConnectionTest extends Unipag_Test_TestCase
{
    public function testBasic()
    {
        $conns = Unipag_Connection::filter(array('enabled' => true));
        $this->assertGreaterThan(1, count($conns));

        $conn = Unipag_Connection::get($conns[0]->id);
        $this->assertEquals($conn->payment_gateway, $conns[0]->payment_gateway);
    }
}
