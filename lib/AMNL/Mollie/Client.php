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
 * @copyright Copyright (c) 2012, Arno Moonen <info@arnom.nl>
 * @package AMNL-Mollie
 */

namespace AMNL\Mollie;

use AMNL\Mollie\Exception\MollieException;
use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
abstract class Client
{

    /**
     * @var Buzz\Browser
     */
    private $browser;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string User-Agent
     */
    protected static $userAgentString;

    /**
     *
     * @param string $base_url Webservice API base. The path will be appended to this.
     * @param ClientInterface $client Client to be used by the Browser instance
     */
    public function __construct($base_url, ClientInterface $client = null)
    {
        $this->baseUrl = $base_url;
        $this->browser = new Browser($client);
    }

    /**
     * @param ClientInterface $client Client to be used by the Browser instance
     */
    public function setClient(ClientInterface $client)
    {
        $this->browser->setClient($client);
    }

    /**
     *
     * @param string $path Path (will be appended to the Base URL)
     * @param array $params
     * @param mixed $method
     * @return \SimpleXMLElement
     */
    protected function request($path, array $params = null, $method = RequestInterface::METHOD_GET)
    {
        // Full URL
        $fullUrl = new Url($this->baseUrl . $path);

        // Additional headers
        $headers = array(
            'User-Agent: ' . $this->getUserAgentString(),
        );

        // Got data?
        if ($method === RequestInterface::METHOD_GET) {
            if ($params != null && count($params) > 0) {
                $fullUrl = new Url($this->baseUrl . $path . '?' . http_build_query($params));
            }
            $response = $this->browser->get($fullUrl, $headers);
        } else {
            $response = $this->browser->call($fullUrl, $method, $headers, $params);
        }

        // Convert XML
        $xml = @simplexml_load_string($response->getContent());

        // Succesful?
        if ($xml === false) {
            throw new MollieException('Server did not respond with valid XML.');
        }

        // Error?
        if ($xml->item != null && ((string) $xml->item['type']) == 'error') {
            throw new MollieException((string) $xml->item->message, intval($xml->item->errorcode));
        }

        return $xml;
    }

    private function getUserAgentString()
    {
        if (null === static::$userAgentString) {
            // Create User-Agent string
            static::$userAgentString = get_class($this) . ' (' . base_convert(filemtime(__FILE__), 10, 36) . ';PHP/' . PHP_VERSION . ')';
        }

        return static::$userAgentString;
    }

}
