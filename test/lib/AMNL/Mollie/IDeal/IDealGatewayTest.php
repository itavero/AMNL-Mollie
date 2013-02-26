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
     * @var IDealTestGatewayImpl
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new IDealTestGatewayImpl();
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
     * @todo   Implement testCheckPayment().
     */
    public function testCheckPaymentHandleResponse()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::preparePayment
     * @todo   Implement testPreparePayment().
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
        $this->object->setBrowserImplementation($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\ProviderResponse('482d599bbcc7795727650330ad65fe9b', 1234, 'https://secure.somewhere/');
        $this->assertEquals($expected, $this->object->preparePayment(1234, 'http://some.where/a', 'http://some.where/b', 'description', array('bank' => '0721')));
    }

    /**
     * @covers AMNL\Mollie\IDeal\IDealGateway::getBankList
     * @todo   Implement testGetBankList().
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
        $this->object->setBrowserImplementation($mockBrowser);

        // Test
        $needle = new Bank(721, 'Postbank');
        $actual = $this->object->getBankList();
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
        return $browser;
    }

}

/**
 * Implementation of AMNL\Mollie\IDeal\IDealGateway which exposes
 * a method to set the browser implementation.
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class IDealTestGatewayImpl extends IDealGateway
{

    public function __construct()
    {
        parent::__construct(123456, 'abcdef123456789');
    }

    public function setBrowserImplementation(Browser $b)
    {
        $this->browser = $b;
    }

}