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

namespace AMNL\Mollie\IVR;

use Buzz\Message\RequestInterface;
use Buzz\Browser;

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class MicropaymentGatewayTest extends \PHPUnit_Framework_TestCase
{
    // TODO Add tests that fail to check error handling
    // TODO Add tests to check the request that is formed

    /**
     * @var MicropaymentTestGatewayImpl
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MicropaymentTestGatewayImpl();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::preparePayment
     * @todo   Implement testPreparePayment().
     */
    public function testPreparePaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <item country="31">
        <servicenumber>0909-1100400</servicenumber>
        <paycode>012345</paycode>
        <amount>1.75</amount>
        <duration>131</duration>
        <mode>ppm</mode>
        <costperminute>0.80</costperminute>
        <payout>1.09</payout>
        <currency>euro</currency>
    </item>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowserImplementation($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\IVR\MicropaymentResponse('0909-1100400', '012345', MicropaymentGateway::MODE_PAYPERMINUTE, 80, 109, 175, 131);
        $this->assertEquals($expected, $this->object->preparePayment(175));
    }

    /**
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::checkPayment
     * @todo   Implement testCheckPayment().
     */
    public function testCheckPaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0"?>
<response>
    <item country="31">
        <servicenumber>0909-1100400</servicenumber>
        <paycode>012345</paycode>
        <payed>true</payed>
        <durationdone>131</durationdone>
        <durationleft>0</durationleft>
        <mode>ppm</mode>
        <amount>1.75</amount>
        <currency>euro</currency>
        <paystatus>Payment done.</paystatus>
    </item>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowserImplementation($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\IVR\MicropaymentStatus('0909-1100400', '012345', MicropaymentGateway::MODE_PAYPERMINUTE, 175, 'Payment done.', true, false, 131, 0);
        $this->assertEquals($expected, $this->object->checkPayment('0909-1100400', '012345'));
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
 * Implementation of AMNL\Mollie\IVR\MicropaymentGateway which exposes
 * a method to set the browser implementation.
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class MicropaymentTestGatewayImpl extends MicropaymentGateway
{

    public function __construct()
    {
        parent::__construct(123456);
    }

    public function setBrowserImplementation(Browser $b)
    {
        $this->browser = $b;
    }

}