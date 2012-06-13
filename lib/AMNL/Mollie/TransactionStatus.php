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
class TransactionStatus
{

    /**
     *
     * @var int Amount in cents
     */
    protected $amount;

    /**
     *
     * @var boolean True if it has been paid; false otherwise
     */
    protected $paid;

    /**
     *
     * @var string Transaction ID
     */
    protected $transactionId;

    /**
     *
     * @var string Status Message
     */
    protected $status;

    /**
     *
     * @param int $amount
     * @param boolean $paid
     * @param string $transaction_id
     * @param string $status
     */
    public function __construct($amount, $paid, $transaction_id = null, $status = null)
    {
        $this->amount = intval($amount);
        $this->paid = ($paid != false);
        $this->transactionId = $transaction_id;
        $this->status = $status;
    }

    /**
     *
     * @return int Amount in cents
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * @return boolean True if transaction is paid; false otherwise
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     *
     * @return string Transaction ID
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     *
     * @return string Status Message
     */
    public function getStatus()
    {
        return $this->status;
    }

}
