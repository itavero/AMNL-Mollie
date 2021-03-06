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

namespace AMNL\Mollie\Exception;

/**
 * This exception class serves as a base class for all other
 * exceptions thrown by this library and will be thrown when there is
 * no specific exception class available for the situation.
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class MollieException extends \RuntimeException
{

}