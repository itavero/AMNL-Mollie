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

namespace AMNL\Mollie\MiniTix;

use AMNL\Mollie\BaseGateway;
use AMNL\Mollie\ProviderResponse;
use AMNL\Mollie\TransactionStatus;
use AMNL\Mollie\Exception\MollieException;
use Buzz\Client\ClientInterface;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class MiniTixGateway extends BaseGateway
{

    const COMMUNITY_HYVES = 'hyvesafrekenen';
    const COMMUNITY_RABOSMS = 'rabosmsbetalen';

    public function __construct($partner_id, $profile_key, $base_url = 'https://secure.mollie.nl/xml/', ClientInterface $client = null)
    {
        parent::__construct($partner_id, $profile_key, $base_url, $client);
    }

    public function checkPayment($transaction_id)
    {
        // Check arguments
        if (null === $transaction_id || !is_string($transaction_id) || strlen(trim($transaction_id)) == 0) {
            throw new \InvalidArgumentException('Transaction ID should be a string.');
        }

        // Params
        $params = array(
            'action' => 'check',
            'partnerid' => $this->partnerId,
            'transaction_id' => $transaction_id,
        );

        // Do request
        $xml = $this->request('minitix', $params);

        // Gather data
        if ($xml->order === null) {
            // TODO Add a fancy error message
            throw new MollieException();
        }
        $transactionId = (string) $xml->order->transaction_id;
        $amount = (int) $xml->order->amount;
        $paid = ((string) $xml->order->paid == 'true');

        // Return TransactionStatus
        return new TransactionStatus($amount, $paid, $transaction_id);
    }

    /**
     * Create/prepare a new MiniTix transaction.
     *
     * @param int $amount Amount (in eurocents)
     * @param string $report_url Reporting URL
     * @param string $return_url Return URL (for Customer)
     * @param string $description Description (visible to Customer)
     * @param string $community Community (use one of the constants prefixed with COMMUNITY_)
     * @return ProviderResponse
     */
    public function prepareMiniTixPayment($amount, $report_url, $return_url, $description, $community)
    {
        return $this->preparePayment($amount, $report_url, $return_url, $description, array('community' => $community));
    }

    /**
     * Create/prepare a new MiniTix transaction.
     *
     * @param int $amount Amount (in eurocents)
     * @param string $report_url Reporting URL
     * @param string $return_url Return URL (for Customer)
     * @param string $description Description (visible to Customer)
     * @param array $options Additional options. Should containt a Community (key: 'community', type: 'string')
     * @return ProviderResponse
     */
    public function preparePayment($amount, $report_url, $return_url, $description, array $options = null)
    {
        // Check arguments
        if (!filter_var($amount, FILTER_VALIDATE_INT) || $amount < 24 || $amount > 15000) {
            throw new \InvalidArgumentException('Amount should be in eurocents, at least 24 eurocents and at most 15000 eurocents.');
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
        if (!is_array($options) || !array_key_exists('community', $options) || !($options['community'] === self::COMMUNITY_HYVES || $options['community'] === self::COMMUNITY_RABOSMS)) {
            throw new \InvalidArgumentException('No Community supplied.');
        }

        // Description
        $description = trim(strval($description));
        if (preg_match('/[<|>]/', $description) > 0) {
            trigger_error('Given description contains a greater-than or less-than sign. These will be filtered.', E_USER_NOTICE);
            $description = trim(preg_replace('/([<|>])/is', '', $description));
        }
        if (strlen($description) > 20) {
            trigger_error('Given description exceeds 20 characters. Excess characters will be removed.', E_USER_NOTICE);
            $description = substr($description, 0, 20);
        }

        // Params
        $params = array(
            'action' => 'prepare',
            'partnerid' => $this->partnerId,
            'profile_key' => $this->profileKey,
            'amount' => intval($amount),
            'type' => $options['community'],
            'description' => $description,
            'reporturl' => $report_url,
            'returnurl' => $return_url,
        );

        // Do request
        $xml = $this->request('minitix', $params);

        // Gather data
        if ($xml->order === null) {
            // TODO Add a fancy error message
            throw new MollieException();
        }
        $transactionId = (string) $xml->order->transaction_id;
        $amount = (int) $xml->order->amount;
        $destination = (string) $xml->order->URL;

        // Return ProviderResponse
        return new ProviderResponse($transactionId, $amount, $destination);
    }

}
