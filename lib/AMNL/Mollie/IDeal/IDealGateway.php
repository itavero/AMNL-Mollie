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

use AMNL\Mollie\BaseGateway;
use AMNL\Mollie\ProviderResponse;
use AMNL\Mollie\Exception\MollieException;
use Buzz\Message\RequestInterface;
use Buzz\Client\ClientInterface;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class IDealGateway extends BaseGateway
{

    /**
     * @var array Available banks
     */
    private static $listOfBanks;

    /**
     * @var boolean Testmode active?
     */
    private $testMode;

    public function __construct($partner_id, $profile_key = null, $testmode = true, $base_url = 'https://secure.mollie.nl/xml/', ClientInterface $client = null)
    {
        parent::__construct($partner_id, $profile_key, $base_url, $client);
        $this->testMode = $testmode;
    }

    public function isUsingTestMode()
    {
        return $this->testMode;
    }

    public function checkPayment($transaction_id)
    {
        // Check arguments
        if (null === $transaction_id || !is_string($transaction_id) || strlen(trim($transaction_id)) == 0) {
            throw new \InvalidArgumentException('Transaction ID should be a string.');
        }

        // Params
        $params = array(
            'a' => 'check',
            'partnerid' => $this->partnerId,
            'transaction_id' => $transaction_id,
            'testmode' => ($this->testMode) ? 'true' : 'false',
        );

        // Do request
        $xml = $this->request('ideal', $params);

        // Gather data
        if ($xml->order === null) {
            // TODO Add a fancy error message
            throw new MollieException();
        }
        $transactionId = (string) $xml->order->transaction_id;
        $amount = (int) $xml->order->amount;
        $paid = ((string) $xml->order->payed == 'true');
        $status = (string) $xml->order->status;

        // Consumer data?
        $consumer = null;
        if ($paid && $xml->order->consumer != null) {
            $consXml = $xml->order->consumer;
            $consumer = new Consumer((string) $consXml->consumerName, (string) $consXml->consumerAccount, (string) $consXml->consumerCity);
            unset($consXml);
        }

        // Return IDealTransactionStatus
        return new IDealTransactionStatus($amount, $paid, $transaction_id, $consumer, $status);
    }

    /**
     * Create/prepare a new iDEAL transaction.
     *
     * @param int $amount Amount (in eurocents)
     * @param string $report_url Reporting URL
     * @param string $return_url Return URL (for Customer)
     * @param string $description Description (visible to Customer)
     * @param Bank|int $bank Bank-object or a Bank ID
     * @return ProviderResponse
     */
    public function prepareIDealPayment($amount, $report_url, $return_url, $description, $bank)
    {
        return $this->preparePayment($amount, $report_url, $return_url, $description, array('bank' => $bank));
    }

    /**
     * Create/prepare a new iDEAL transaction.
     *
     * @param int $amount Amount (in eurocents)
     * @param string $report_url Reporting URL
     * @param string $return_url Return URL (for Customer)
     * @param string $description Description (visible to Customer)
     * @param array $options Additional options. Should containt a Bank / int (key: 'bank')
     * @return ProviderResponse
     */
    public function preparePayment($amount, $report_url, $return_url, $description, array $options = null)
    {
        // Check arguments
        if (!filter_var($amount, FILTER_VALIDATE_INT) || $amount < 118) {
            throw new \InvalidArgumentException('Amount should be in eurocents and at least 118 cents.');
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
        if (!is_array($options) || !array_key_exists('bank', $options) || !($options['bank'] instanceof Bank || is_int($options['bank']) || is_string($options['bank']))) {
            throw new \InvalidArgumentException('No Bank instance or Bank ID supplied.');
        }
        if (strlen($description) > 29) {
            trigger_error('Given description exceeds 29 characters. Excess characters will be removed.', E_USER_NOTICE);
            $description = substr(trim($description), 0, 29);
        }

        // Bank
        $bank = $options['bank'];

        // Params
        $params = array(
            'a' => 'fetch',
            'partnerid' => $this->partnerId,
            'bank_id' => ($bank instanceof Bank) ? $bank->getId() : $bank,
            'amount' => $amount,
            'description' => $description,
            'reporturl' => $report_url,
            'returnurl' => $return_url,
        );

        // Profile Key
        if ($this->profileKey != null) {
            $params['profile_key'] = $this->profileKey;
        }

        // Do request
        $xml = $this->request('ideal', $params);

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

    /**
     * Returns an array of Bank-instances the customer can choose from.
     *
     * @param boolean $use_cache Use internal array cache?
     * @return array An array containing Bank-instances
     */
    public function getBankList($use_cache = true)
    {
        if ($use_cache && self::$listOfBanks != null) {
            return self::$listOfBanks;
        }

        // Params
        $params = array(
            'a' => 'banklist',
            'testmode' => ($this->testMode) ? 'true' : 'false',
        );

        // Do request
        $xml = $this->request('ideal', $params);

        // List of banks
        $banks = array();

        foreach ($xml->bank as $bank) {
            $banks[] = new Bank($bank->bank_id, $bank->bank_name);
        }

        // Cache?
        if ($use_cache) {
            self::$listOfBanks = $banks;
        }

        return $banks;
    }

}
