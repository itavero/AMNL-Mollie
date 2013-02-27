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

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class MicropaymentGatewayTest extends \AMNL\Mollie\Test\WebServiceTestCase
{

    /**
     * @var MicropaymentGateway
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MicropaymentGateway(123456);
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
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::preparePayment
     */
    public function testPreparePaymentCreateRequest()
    {
        // Do request
        try {
            $this->object->preparePayment(234, 'https://some.where', 31, '0909-1100400');
        }
        catch (\Exception $e) {
            // Exceptions aren't really important.
            // We're only interested in the request that has
            // been created.
        }

        // Test last request
        $request = $this->object->getBrowser()->getLastRequest();
        $this->assertEquals(\Buzz\Message\Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('https://www.mollie.nl', $request->getHost());
        $this->assertStringStartsWith('/xml/micropayment', $request->getResource());
        $this->assertContains('a=fetch', $request->getResource());
        $this->assertContains('partnerid=123456', $request->getResource());
        $this->assertContains('amount=2.34', $request->getResource());
        $this->assertContains('servicenumber=0909-1100400', $request->getResource());
        $this->assertContains('report=http', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::preparePayment
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
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\IVR\MicropaymentResponse('0909-1100400', '012345', MicropaymentGateway::MODE_PAYPERMINUTE, 80, 109, 175, 131);
        $this->assertEquals($expected, $this->object->preparePayment(175));
    }

    /**
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::checkPayment
     */
    public function testCheckPaymentCreateRequest()
    {
        // Do request
        try {
            $this->object->checkPayment('0909-1100400', '012345');
        }
        catch (\Exception $e) {
            // Exceptions aren't really important.
            // We're only interested in the request that has
            // been created.
        }

        // Test last request
        $request = $this->object->getBrowser()->getLastRequest();
        $this->assertEquals(\Buzz\Message\Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('https://www.mollie.nl', $request->getHost());
        $this->assertStringStartsWith('/xml/micropayment', $request->getResource());
        $this->assertContains('a=check', $request->getResource());
        $this->assertContains('servicenumber=0909-1100400', $request->getResource());
        $this->assertContains('paycode=012345', $request->getResource());
    }

    /**
     * @covers AMNL\Mollie\IVR\MicropaymentGateway::checkPayment
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
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\IVR\MicropaymentStatus('0909-1100400', '012345', MicropaymentGateway::MODE_PAYPERMINUTE, 175, 'Payment done.', true, false, 131, 0);
        $this->assertEquals($expected, $this->object->checkPayment('0909-1100400', '012345'));
    }

}