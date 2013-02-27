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

namespace AMNL\Mollie\MiniTix;

use Buzz\Message\RequestInterface;
use Buzz\Browser;

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class MiniTixGatewayTest extends \PHPUnit_Framework_TestCase
{
    // TODO Add tests that fail to check error handling
    // TODO Add tests to check the request that is formed

    /**
     * @var MiniTixGateway
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MiniTixGateway(123456, 'abcdef123456789');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers AMNL\Mollie\MiniTix\MiniTixGateway::checkPayment
     * @todo   Implement testCheckPayment().
     */
    public function testCheckPaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0" ?>
<response>
 <order>
  <transaction_id>900150983cd24fb0d6963f7d28e17f72</transaction_id>
  <amount>1234</amount>
  <currency>EUR</currency>
  <paid>true</paid>
  <message>This MiniTix-payment has been succesfully paid for and this is the first time you checked it.</message>
 </order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\TransactionStatus(1234, true, '900150983cd24fb0d6963f7d28e17f72');
        $this->assertEquals($expected, $this->object->checkPayment('900150983cd24fb0d6963f7d28e17f72'));
    }

    /**
     * @covers AMNL\Mollie\MiniTix\MiniTixGateway::preparePayment
     * @todo   Implement testPreparePayment().
     */
    public function testPreparePaymentHandleResponse()
    {
        // Mock Browser
        $responseContent = <<<XML
<?xml version="1.0" ?>
<response>
	<order>
		<transaction_id>482ba0d32103bfe8eb9bca363d3edd89</transaction_id>
		<amount>1234</amount>
		<currency>EUR</currency>
		<URL>https://secure.somewhere/</URL>
		<message>Your MiniTix-payment has been successfully setup.</message>
	</order>
</response>
XML;
        $mockBrowser = $this->createMockBrowserWithResponse($responseContent);
        $this->object->setBrowser($mockBrowser);

        // Test
        $expected = new \AMNL\Mollie\ProviderResponse('482ba0d32103bfe8eb9bca363d3edd89', 1234, 'https://secure.somewhere/');
        $this->assertEquals($expected, $this->object->preparePayment(1234, 'http://test.somewhere/a', 'http://test.somewhere/b', 'description', array('community' => MiniTixGateway::COMMUNITY_HYVES)));
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