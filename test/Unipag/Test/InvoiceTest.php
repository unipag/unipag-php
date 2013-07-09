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
            'currency' => 'RUB',
            'customer' => 'Foo',
            'description' => 'Bar',
        ));
        $this->assertEquals(1, $invoice->amount);
        $this->assertEquals('RUB', $invoice->currency);
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
        $payment = Unipag_Payment::create(array(
            'invoice' => $invoice->id,
            'payment_gateway' => 'masterbank.ru',
            'params' => array(
                'description' => 'Herp Derp'
            )
        ));
        $this->assertFalse($payment->cancelled);
        $invoice->remove();
        $invoice->reload();
        $this->assertTrue($invoice->deleted);
        $payment->reload();
        $this->assertEquals('Herp Derp', $payment->params['description']);
        $this->assertTrue($payment->cancelled);
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
     * @depends testRestore
     */
    public function testRemove2($invoice)
    {
        $this->assertFalse($invoice->deleted);
        $payment = Unipag_Payment::create(array(
            'invoice' => $invoice->id,
            'payment_gateway' => 'masterbank.ru',
        ));
        $this->assertFalse($payment->cancelled);
        $invoice->deleted = true;
        $invoice->save();
        $invoice->reload();
        $this->assertTrue($invoice->deleted);
        $payment->reload();
        $this->assertTrue($payment->cancelled);
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

    public function testCustomData()
    {
        $test_array = array(
            'int' => 1,
            'float' => 3.14159,
            'str' => 'Hi there',
            'null' => NULL,
            'true' => True,
            'false' => False,
            'int_str' => '2',
            'float_str' => '2.71828',
            'null_str' => 'null',
            'true_str' => 'true',
            'false_str' => 'false',
            'array' => array(1, 1.5, 'a', NULL, True),
            'obj' => array(
                'int_key' => 42,
                'float_key' => 90.0,
                'str_key' => '®',
                'array' => array(1, 2, False),
                'obj' => array('null' => NULL)
            ),
        );
        $invoice = new Unipag_Invoice(array(
            'amount' => 1.5,
            'currency' => 'USD',
            'customer' => '®',
            'custom_data' => $test_array,
        ));
        $invoice->save();
        $this->assertEquals('®', $invoice->customer);
        $inv2 = Unipag_Invoice::get($invoice->id);
        $this->assertEquals('®', $inv2->customer);
        $this->assertEquals($test_array, $inv2->custom_data);
    }
}
