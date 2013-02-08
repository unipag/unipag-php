<?php

class Unipag_Test_ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testInvoiceFromJson()
    {
        $invoice_created = new Unipag_Invoice(array(
            'id' => 1,
            'amount' => 1.01,
            'currency' => 'USD',
        ));
        $this->assertEquals(1.01, $invoice_created->amount);
        $this->assertEquals('USD', $invoice_created->currency);

        $invoice_constructed = Unipag_Object::fromJson(
            '{
                "object": "invoice",
                "id": 1,
                "amount": 1.01,
                "currency": "USD"
             }'
        );
        $this->assertEquals($invoice_created, $invoice_constructed);
    }

    public function testManyInvoicesFromJson()
    {
        $invoices_created = array(
            new Unipag_Invoice(array(
                'id' => 1,
                'amount' => 1.01,
                'currency' => 'USD',
            )),
            new Unipag_Invoice(array(
                'id' => 2,
                'amount' => 2.02,
                'currency' => 'EUR',
            )),
        );
        $invoices_constructed = Unipag_Object::fromJson(
            '[
                 {
                    "object": "invoice",
                    "id": 1,
                    "amount": 1.01,
                    "currency": "USD"
                 },
                 {
                    "object": "invoice",
                    "id": 2,
                    "amount": 2.02,
                    "currency": "EUR"
                 }
             ]'
        );
        $this->assertEquals($invoices_created, $invoices_constructed);
    }

    public function testEventFromJson()
    {
        $event_created = new Unipag_Event(array(
            'id' => 1,
            'related_object' => new Unipag_Invoice(array(
                'id' => 2,
                'amount' => 3,
                'currency' => 'EUR',
            )),
        ));
        $event_constructed = Unipag_Object::fromJson(
            '{
                "object": "event",
                "id": 1,
                "related_object": {
                    "object": "invoice",
                    "id": 2,
                    "amount": 3.0,
                    "currency": "EUR"
                }
             }'
        );
        $this->assertEquals($event_created, $event_constructed);
    }
}
