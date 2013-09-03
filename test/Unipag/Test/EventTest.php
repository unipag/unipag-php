<?php

require_once 'TestCase.php';

class Unipag_Test_EventTest extends Unipag_Test_TestCase
{
    public function testBasic()
    {
        // Generate 2 events - invoice.created and invoice.modified
        $inv = Unipag_Invoice::create(array(
            'amount' => 1,
            'currency' => 'RUB'
        ));
        $inv->amount = 42;
        $inv->save();

        $events = Unipag_Event::filter(array(
            'invoice' => $inv->id,
            'type' => 'invoice.changed',
        ));
        $this->assertEquals(1, count($events));
        $this->assertEquals('invoice.changed', $events[0]->type);

        $event = Unipag_Event::get($events[0]->id);
        $invoice = $event->related_object;
        $this->assertTrue(is_a($invoice, 'Unipag_Invoice'));
        $this->assertEquals(42, $invoice->amount);
    }
}