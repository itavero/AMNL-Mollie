<?php

/**
 * This file is part of AMNL-Mollie.
 *
 * (c) Arno Moonen <info@arnom.nl>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Arno Moonen <info@arnom.nl>
 * @copyright Copyright (c) 2013, Arno Moonen <info@arnom.nl>
 * @package AMNL-Mollie
 */

namespace AMNL\Mollie\Paysafecard;

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class PaysafecardGatewayTest extends \AMNL\Mollie\Test\WebServiceTestCase
{

    /**
     * @var PaysafecardGateway
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PaysafecardGateway(123456, 'abcdef123456789');
        $this->object->setClient(new \AMNL\Mollie\Test\BuzzMockClient());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::checkPayment
     */
    public function testCheckPaymentCreateRequest()
    {
        // Do request
        try {
            $this->object->checkPayment('abcdef');
        }
        catch (\Exception $e) {
            // Exceptions aren't really important.
            // We're only interested in the request that has
            // been created.
        }

        // Test last request
        $request = $this->object->getBrowser()->getLastRequest();
        $this->assertEquals(\Buzz\Message\Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('https://secure.mollie.nl', $request->getHost());
        $this->assertStringStartsWith('/xml/paysafecard/check-status/', $request->getResource());
        $this->assertContains('partnerid=123456', $request->getResource());
        $this->assertContains('transaction_id=abcdef', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::checkPayment
     */
    public function testCheckPaymentHandleResponse()
    {// Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <order>
        <transaction_id>482d599bbcc7795727650330ad65fe9b</transaction_id>
        <amount>123</amount>
        <paid>true</paid>
        <status>Completed</status>
    </order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\TransactionStatus(123, true, '482d599bbcc7795727650330ad65fe9b', 'Completed');
        $this->assertEquals($expected, $this->object->checkPayment('482d599bbcc7795727650330ad65fe9b'));
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterCustomerRefCapital()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(1234, 'http://some.where/a', 'http://some.where/b', 'Abcd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterCustomerRefWhitespace()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(1234, 'http://some.where/a', 'http://some.where/b', 'ab cd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterAmount()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(99, 'http://some.where/a', 'http://some.where/b', 'abcd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterReturnUrl()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(123, 'http://some.where/a', 'not a url', 'abcd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterReportUrl()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(123, 'not a url', 'http://some.where/b', 'abcd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentParameterSameUrls()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->preparePayment(123, 'http://some.where/', 'http://some.where/', 'abcd');
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentCreateRequest()
    {
        // Do request
        try {
            $this->object->preparePayment(1234, 'https://some.where/a', 'https://some.where/b', 'abc');
        }
        catch (\Exception $e) {
            // Exceptions aren't really important.
            // We're only interested in the request that has
            // been created.
        }

        // Test last request
        $request = $this->object->getBrowser()->getLastRequest();
        $this->assertEquals(\Buzz\Message\Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('https://secure.mollie.nl', $request->getHost());
        $this->assertStringStartsWith('/xml/paysafecard/prepare/', $request->getResource());
        $this->assertContains('partnerid=123456', $request->getResource());
        $this->assertContains('amount=1234', $request->getResource());
        $this->assertContains('customer_ref=abc', $request->getResource());
        $this->assertContains('profile_key=abcdef123456789', $request->getResource());
        $this->assertContains('reporturl=https', $request->getResource());
        $this->assertContains('returnurl=https', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\Paysafecard\PaysafecardGateway::preparePayment
     */
    public function testPreparePaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <order>
        <transaction_id>482d599bbcc7795727650330ad65fe9b</transaction_id>
        <amount>1234</amount>
        <currency>EUR</currency>
        <URL>https://secure.somewhere/</URL>
    </order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\ProviderResponse('482d599bbcc7795727650330ad65fe9b', 1234, 'https://secure.somewhere/');
        $this->assertEquals($expected, $this->object->preparePayment(1234, 'https://some.where/a', 'https://some.where/b', 'abc'));
    }

}
