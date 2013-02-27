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

use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class BuzzMockClient implements ClientInterface
{

    public function send(RequestInterface $request, MessageInterface $response)
    {
        // Do nothing
    }

}
