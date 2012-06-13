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

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class ProviderResponse
{

    /**
     * @var string Transaction ID
     */
    protected $transactionId;

    /**
     * @var int Amount in cents
     */
    protected $amount;

    /**
     * @var string URL to which the user should be forwarded
     */
    protected $destination;

    /**
     * 
     * @param string $transactionId Transaction ID
     * @param int $amount Amount in cents
     * @param string $destination URL to which the user should be forwarded
     */
    public function __construct($transactionId, $amount, $destination)
    {
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->destination = $destination;
    }

    /**
     * @return string Transaction ID
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return int Amount in cents
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string URL to which the user should be forwarded
     */
    public function getDestination()
    {
        return $this->destination;
    }

}
