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

namespace AMNL\Mollie\Test;

use Buzz\Message\RequestInterface;
use Buzz\Browser;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
abstract class WebServiceTestCase extends \PHPUnit_Framework_TestCase
{

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
