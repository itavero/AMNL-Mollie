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
use AMNL\Mollie\Exception\MollieServerErrorException;
use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

/**
 * Client
 *
 * @author Arno Moonen <info@arnom.nl>
 */
abstract class Client
{

    /**
     * @var \Buzz\Browser
     */
    protected $browser;

    /**
     * @var string
     */
    protected $baseUrl;

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
     * @return \Buzz\Client\ClientInterface Client
     */
    public function getClient()
    {
        return $this->browser->getClient();
    }

    /**
     * @return \Buzz\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param \Buzz\Browser $browser
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     *
     * @param string $path Path (will be appended to the Base URL)
     * @param array $params
     * @param mixed $method
     * @return \SimpleXMLElement
     * @throws \AMNL\Mollie\Exception\MollieException
     * @throws \AMNL\Mollie\Exception\MollieServerErrorException
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
        $responseXml = null;
        try {
            $responseXml = new \SimpleXMLElement($response->getContent());
        }
        catch (\Exception $e) {
            throw new MollieException('Failed to convert response content to XML object.', 0, $e);
        }

        // Contains error?
        $this->checkResponseForError($responseXml);

        return $responseXml;
    }

    /**
     * Check if the given response contains an error, if so, it will
     * throw an exception.
     *
     * @param \SimpleXMLElement $response
     * @throws \AMNL\Mollie\Exception\MollieServerErrorException
     */
    protected function checkResponseForError(\SimpleXMLElement $response)
    {
        if ($response->item != null && ((string) $response->item['type']) == 'error') {
            throw new MollieServerErrorException((string) $response->item->message, intval($response->item->errorcode));
        }
    }

    /**
     * @return string Returns the user agent string (and generates it if it does not yet exist)
     */
    private function getUserAgentString()
    {
        if (null === static::$userAgentString) {
            // Create User-Agent string
            static::$userAgentString = get_class($this) . ' (' . base_convert(filemtime(__FILE__), 10, 36) . ';PHP/' . PHP_VERSION . ')';
        }

        return static::$userAgentString;
    }

}