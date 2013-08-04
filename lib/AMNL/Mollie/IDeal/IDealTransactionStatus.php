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

namespace AMNL\Mollie\IDeal;

use AMNL\Mollie\TransactionStatus;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class IDealTransactionStatus extends TransactionStatus
{

    /**
     * @var \AMNL\Mollie\IDeal\Consumer
     */
    protected $consumer;

    /**
     *
     * @param int $amount
     * @param boolean $paid
     * @param string $transaction_id
     * @param Consumer $consumer
     * @param string $status
     */
    public function __construct($amount, $paid, $transaction_id, $consumer, $status)
    {
        // Fix the corky response of Mollie
        // TODO Verify behavior of iDEAL API
        /*if ($paid === false && $status == 'CheckedBefore') {
            $paid = true;
        }*/

        parent::__construct($amount, $paid, $transaction_id, $status);

        $this->consumer = $consumer;
    }

    /**
     * @return Consumer
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

}
