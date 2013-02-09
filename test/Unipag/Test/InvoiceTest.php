<?php

require_once 'Key.php';

class Unipag_Test_InvoiceTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Unipag_Config::$api_key = Unipag_Test_Key::SEC_KEY;
    }

    public function testCreate()
    {
        $invoice = Unipag_Invoice::create(array(
            'amount' => 1,
            'currency' => 'USD',
            'customer' => 'Foo',
            'description' => 'Bar',
        ));
        $this->assertEquals(1, $invoice->amount);
        $this->assertEquals('USD', $invoice->currency);
        $this->assertEquals('Foo', $invoice->customer);
        $this->assertEquals('Bar', $invoice->description);

        return $invoice;
    }

    /**
     * @depends testCreate
     */
    public function testModify($invoice)
    {
        $initial_amount = $invoice->amount;
        $invoice->amount += 1;
        $invoice->save();

        $invoice2 = Unipag_Invoice::get($invoice->id);
        $this->assertEquals($initial_amount + 1, $invoice2->amount);
        return $invoice2;
    }

    /**
     * @depends testCreate
     */
    public function testRemove($invoice)
    {
        $this->assertFalse($invoice->deleted);
        $invoice->remove();
        $this->assertTrue($invoice->deleted);
        return $invoice;
    }

    /**
     * @depends testRemove
     */
    public function testRestore($invoice)
    {
        $this->assertTrue($invoice->deleted);
        $invoice->restore();
        $this->assertFalse($invoice->deleted);
        return $invoice;
    }

    /**
     * Test that we are able to find previously created invoice using filter
     *
     * @depends testCreate
     */
    public function testFilter($invoice)
    {
        $invoice_list = Unipag_Invoice::filter();
        foreach ($invoice_list as $num => $inv) {
            if ($inv->id == $invoice->id) {
                $this->assertTrue(true);
                return $inv;
            }
        }
        $this->fail("Invoice id={$invoice->id} not found.");
    }

    /**
     * Create new invoice using save.
     */
    public function testCreate2()
    {
        $invoice = new Unipag_Invoice(array(
            'amount' => 42,
            'currency' => 'USD',
        ));
        $this->assertEmpty($invoice->id);
        $invoice->save();
        $this->assertNotEmpty($invoice->id);
        return $invoice;
    }

    /**
     * @depends testCreate
     */
    public function testReload($invoice)
    {
        $reloaded = new Unipag_Invoice(array('id' => $invoice->id));
        $reloaded->reload();
        $this->assertEquals($invoice->amount, $reloaded->amount);
    }
}
