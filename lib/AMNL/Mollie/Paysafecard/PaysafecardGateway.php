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

namespace AMNL\Mollie\Paysafecard;

use AMNL\Mollie\BaseGateway;
use AMNL\Mollie\ProviderResponse;
use AMNL\Mollie\TransactionStatus;
use AMNL\Mollie\Exception\MollieException;
use Buzz\Client\ClientInterface;

/**
 * PaysafecardGateway
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class PaysafecardGateway extends BaseGateway
{

    public function __construct($partner_id, $profile_key = null, $base_url = null, ClientInterface $client = null)
    {
        if (null === $base_url) {
            $base_url = 'https://secure.mollie.nl/xml/paysafecard/';
        }
        parent::__construct($partner_id, $profile_key, $base_url, $client);
    }

    /**
     * Check the state of a transaction
     *
     * @param string $transaction_id Transaction ID
     * @return \AMNL\Mollie\IDeal\TransactionStatus
     * @throws \InvalidArgumentException
     * @throws MollieException
     */
    public function checkPayment($transaction_id)
    {
        // Check arguments
        if (null === $transaction_id || !is_string($transaction_id) || strlen(trim($transaction_id)) == 0) {
            throw new \InvalidArgumentException('Transaction ID should be a string.');
        }

        // Params
        $params = array(
            'partnerid' => $this->partnerId,
            'transaction_id' => $transaction_id,
        );

        // Do request
        $response = $this->request('check-status/', $params);

        // Gather data
        if ($response->order === null) {
            throw new MollieException('Unexpected response');
        }
        $transactionId = (string) $response->order->transaction_id;
        $amount = (int) $response->order->amount;
        $paid = ((string) $response->order->paid == 'true');
        $status = (string) $response->order->status;

        // Return TransactionStatus
        return new TransactionStatus($amount, $paid, $transactionId, $status);
    }

    /**
     * Request a new payment
     *
     * @param int $amount Amount in eurocents
     * @param string $report_url URL to call when the state of the payment changes
     * @param string $return_url URL to user is send to after the payment process
     * @param string $customer_ref Unique customer reference
     * @param array $options NOT USED FOR PAYSAFECARD
     * @return \AMNL\Mollie\ProviderResponse
     * @throws \InvalidArgumentException
     * @throws MollieException
     */
    public function preparePayment($amount, $report_url, $return_url, $customer_ref, array $options = null)
    {
        // Check arguments
        if (!filter_var($amount, FILTER_VALIDATE_INT) || $amount < 100) {
            throw new \InvalidArgumentException('Amount should be in eurocents and at least 100 cents.');
        }
        if (!filter_var($report_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Report URL should be a valid URL.');
        }
        if (!filter_var($return_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Return URL should be a valid URL.');
        }
        if ($report_url == $return_url) {
            throw new \InvalidArgumentException('Report URL and Return URL should not be the same.');
        }
        if (preg_match('/[A-Z\s]/', $customer_ref) > 0) {
            throw new \InvalidArgumentException('Customer reference (description) may not contain any capital letters or whitespace.');
        }

        // Params
        $params = array(
            'partnerid' => $this->partnerId,
            'amount' => $amount,
            'customer_ref' => $customer_ref,
            'reporturl' => $report_url,
            'returnurl' => $return_url,
        );

        // Profile Key
        if ($this->profileKey != null) {
            $params['profile_key'] = $this->profileKey;
        }

        // Do request
        $response = $this->request('prepare/', $params);

        // Gather data
        if ($response->order === null) {
            throw new MollieException('Unexpected response');
        }
        $transactionId = (string) $response->order->transaction_id;
        $amount = (int) $response->order->amount;
        $destination = (string) $response->order->URL;

        // Return ProviderResponse
        return new ProviderResponse($transactionId, $amount, $destination);
    }

}
