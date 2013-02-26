<?php

namespace AMNL\Mollie;

use Buzz\Message\RequestInterface;
use Buzz\Browser;

/**
 * @author Arno Moonen <info@arnom.nl>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ClientImpl
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ClientImpl('');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers AMNL\Mollie\Client::request
     */
    public function testRequest()
    {
        // SimpleXMLElement
        $xmlString = <<<XML
<?xml version="1.0" ?>
<response>
	<bank>
		<bank_id>9999</bank_id>
		<bank_name>TBM Bank</bank_name>
	</bank>
<message>This is the current list of banks and their ID's that currently support iDEAL-payments</message>
</response>
XML;
        $xmlElement = new \SimpleXMLElement($xmlString);

        // Response for mock browser
        $response = new \Buzz\Message\Response();
        $response->setContent($xmlElement->asXML());
        $response->setHeaders(array('HTTP/1.1 200 OK'));

        // Mock browser
        $browser = $this->getMockBuilder('Buzz\Browser')
                ->setMethods(array('send'))
                ->getMock();
        $browser
                ->expects($this->any())
                ->method('send')
                ->will($this->returnValue($response));
        $this->object->setBrowserImplementation($browser);

        // Run test
        $this->assertEquals($xmlElement, $this->object->doRequest('/'));
    }

    /**
     * @covers AMNL\Mollie\Client::request
     */
    public function testRequestFail()
    {
        // Set expected exception
        $this->setExpectedException('\AMNL\Mollie\Exception\MollieException');

        // Response for mock browser
        $response = new \Buzz\Message\Response();
        $response->setContent('this is not XML');
        $response->setHeaders(array('HTTP/1.1 200 OK'));

        // Mock browser
        $browser = $this->getMockBuilder('Buzz\Browser')
                ->setMethods(array('send'))
                ->getMock();
        $browser
                ->expects($this->any())
                ->method('send')
                ->will($this->returnValue($response));
        $this->object->setBrowserImplementation($browser);

        // Request
        $this->object->doRequest('/');
    }

}

/**
 * Implementation of AMNL\Mollie\Client which exposes a
 * protected method for testing purposes.
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class ClientImpl extends Client
{

    /**
     * Calls the internal method 'request'
     *
     * @param string $path Path (will be appended to the Base URL)
     * @param array $params
     * @param mixed $method
     * @return \SimpleXMLElement
     */
    public function doRequest($path, array $params = null, $method = RequestInterface::METHOD_GET)
    {
        return parent::request($path, $params, $method);
    }

    public function setBrowserImplementation(Browser $b)
    {
        $this->browser = $b;
    }

}