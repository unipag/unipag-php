<?php

class Unipag_Test_ApiTest extends PHPUnit_Framework_TestCase
{
    private $pub_key, $sec_key;

    public function setUp()
    {
        $this->pub_key = 'p_EzLtip2wkZusq7MDNBHs3C';
        $this->sec_key = 'sdEzLtip2wkZusq7MDNBHs3C';
        Unipag_Config::$api_key = $this->sec_key;
        #Unipag_Config::$api_url = 'https://api.unipag.com/v1';
        Unipag_Config::$api_url = 'http://03.dev.ivelum.com:8001/v1';
    }

    public function testApiDefaults()
    {
        Unipag_Config::$currency = 'USD';
        $invoice = new Unipag_Invoice(array(
            'amount' => 1
        ));
        $this->assertNull($invoice->api_key);
        $this->assertEquals($invoice->currency, 'USD');

        $invoice2 = new Unipag_Invoice(array(
            'amount' => 1,
            'currency' => 'EUR'
        ), 'individual_key');
        $this->assertEquals($invoice2->api_key, 'individual_key');
        $this->assertEquals($invoice2->currency, 'EUR');
    }

    public function testNoApiKey()
    {
        $this->setExpectedException('Unipag_Unauthorized');
        Unipag_Config::$api_key = null;
        Unipag_Api::get('/');
    }

    public function testWrongMethod()
    {
        $this->setExpectedException('Unipag_MethodNotAllowed');
        Unipag_Api::request('put', '/');
    }

    public function testConnectionError()
    {
        $this->setExpectedException('Unipag_ConnectionError');
        Unipag_Config::$api_url = 'https://non-existent.unipag.com';
        Unipag_Api::get('/');
    }

    public function testWrongResponse()
    {
        $this->setExpectedException('Unipag_ApiError');
        Unipag_Config::$api_url = 'https://invoice.unipag.com';
        Unipag_Api::get('/');
    }

    public function testApiHttpMethods()
    {
        $invoice = Unipag_Api::post('invoices', array(
            'amount' => 1.01,
            'currency' => 'USD',
        ));
        $this->assertEquals($invoice['amount'], 1.01);
        $this->assertEquals($invoice['currency'], 'USD');

        $deleted = Unipag_Api::delete('invoices/'.$invoice['id']);
        $this->assertEquals($deleted['deleted'], true);

        $get_deleted = Unipag_Api::get('invoices/'.$invoice['id']);
        $this->assertEquals($deleted, $get_deleted);
    }

    public function testApiUnauthorized()
    {
        $this->setExpectedException('Unipag_Unauthorized');
        Unipag_Config::$api_key = 'non-existing-key';
        Unipag_Api::get('/');
    }

    public function testApiWrongUrl()
    {
        $this->setExpectedException('Unipag_NotFound');
        Unipag_Api::get('non-existing-method');
    }

    public function testApiNotFound()
    {
        $this->setExpectedException('Unipag_NotFound');
        Unipag_Api::get('invoices/111111111111404');
    }

    public function testApiBadRequest()
    {
        $this->setExpectedException('Unipag_BadRequest');
        Unipag_Api::post('invoices');
    }
}
