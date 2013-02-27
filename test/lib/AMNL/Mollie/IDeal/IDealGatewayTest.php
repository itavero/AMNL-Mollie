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

namespace AMNL\Mollie\IDeal;

use Buzz\Message\RequestInterface;
use Buzz\Browser;

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class IDealGatewayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var IDealGateway
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new IDealGateway(123456, 'abcdef123456789');
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
     * @covers AMNL\Mollie\IDeal\IDealGateway::checkPayment
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
        $this->assertStringStartsWith('/xml/ideal', $request->getResource());
        $this->assertContains('a=check', $request->getResource());
        $this->assertContains('partnerid=123456', $request->getResource());
        $this->assertContains('transaction_id=abcdef', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::checkPayment
     */
    public function testCheckPaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <order>
        <transaction_id>482d599bbcc7795727650330ad65fe9b</transaction_id>
        <amount>123</amount>
        <currency>EUR</currency>
        <payed>true</payed>
        <consumer>
            <consumerName>Hr J Janssen</consumerName>
            <consumerAccount>P001234567</consumerAccount>
            <consumerCity>Amsterdam</consumerCity>
        </consumer>
        <status>Success</status>
        <message>This iDEAL-order has successfuly been payed for, and this is the first time you check it.</message>
    </order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\IDeal\IDealTransactionStatus(
                        123,
                        true,
                        '482d599bbcc7795727650330ad65fe9b',
                        new \AMNL\Mollie\IDeal\Consumer('Hr J Janssen', 'P001234567', 'Amsterdam'),
                        'Success');
        $this->assertEquals($expected, $this->object->checkPayment('482d599bbcc7795727650330ad65fe9b'));
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::preparePayment
     */
    public function testPreparePaymentCreateRequest()
    {
        // Do request
        try {
            $this->object->prepareIDealPayment(1234, 'https://some.where/a', 'https://some.where/b', 'abc', '9999');
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
        $this->assertStringStartsWith('/xml/ideal', $request->getResource());
        $this->assertContains('a=fetch', $request->getResource());
        $this->assertContains('partnerid=123456', $request->getResource());
        $this->assertContains('bank_id=9999', $request->getResource());
        $this->assertContains('amount=1234', $request->getResource());
        $this->assertContains('description=abc', $request->getResource());
        $this->assertContains('profile_key=abcdef123456789', $request->getResource());
        $this->assertContains('reporturl=https', $request->getResource());
        $this->assertContains('returnurl=https', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::preparePayment
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
        <message>Your iDEAL-payment has succesfuly been setup. Your customer should visit the given URL to make the payment</message>
    </order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\ProviderResponse('482d599bbcc7795727650330ad65fe9b', 1234, 'https://secure.somewhere/');
        $this->assertEquals($expected, $this->object->preparePayment(1234, 'http://some.where/a', 'http://some.where/b', 'description', array('bank' => '0721')));
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::getBankList
     */
    public function testGetBankListRealRequest()
    {
        $this->object->setClient(new \Buzz\Client\FileGetContents());
        $banks = $this->object->getBankList(false);
        $this->assertContainsOnlyInstancesOf('\AMNL\Mollie\IDeal\Bank', $banks);
        $this->assertCount(1, $banks);
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::getBankList
     */
    public function testGetBankListCreateRequest()
    {
        // Do request
        try {
            $this->object->getBankList(false);
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
        $this->assertStringStartsWith('/xml/ideal', $request->getResource());
        $this->assertContains('a=banklist', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::getBankList
     */
    public function testGetBankListHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <bank>
        <bank_id>0031</bank_id>
        <bank_name>ABN AMRO</bank_name>
    </bank>
    <bank>
        <bank_id>0721</bank_id>
        <bank_name>Postbank</bank_name>
    </bank>
    <bank>
        <bank_id>0021</bank_id>
        <bank_name>Rabobank</bank_name>
    </bank>
    <message>This is the current list of banks and their ID's that currently support iDEAL-payments</message>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $needle = new Bank(721, 'Postbank');
        $actual = $this->object->getBankList(false);
        $this->assertContainsOnlyInstancesOf('\AMNL\Mollie\IDeal\Bank', $actual);
        $this->assertCount(3, $actual);
        $this->assertTrue(in_array($needle, $actual));
    }

    protected function createMockBrowserWithResponse($content, array $headers = array('HTTP/1.1 200 OK'))
    {
        // Response for mock browser
        $response = new \Buzz\Message\Response();
        $response->setContent($content);
        $response->setHeaders($headers);

        // Mock browser
        $browser = $this->getMockBuilder('Buzz\Browser')
                ->setMethods(array('send'))
                ->getMock();
        $browser
                ->expects($this->any())
                ->method('send')
                ->will($this->returnValue($response));

        // Mock client
        $this->object->setClient(new \AMNL\Mollie\Test\BuzzMockClient());

        return $browser;
    }

}