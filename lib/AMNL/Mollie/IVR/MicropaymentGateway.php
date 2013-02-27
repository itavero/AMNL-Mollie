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

namespace AMNL\Mollie\IVR;

use AMNL\Mollie\Client;
use AMNL\Mollie\Exception\MollieException;
use Buzz\Client\ClientInterface;

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class MicropaymentGateway extends Client
{

    const MODE_PAYPERCALL = 'ppc';
    const MODE_PAYPERMINUTE = 'ppm';

    /**
     *
     * @var int Mollie account number
     */
    protected $partnerId;

    /**
     *
     * @var string Mollie payment profile key
     */
    protected $profileKey;

    /**
     *
     * @param int $partner_id Mollie Account Number
     * @param string $base_url Webservice API base. The path will be appended to this.
     * @param ClientInterface $client Client to be used by the Browser instance
     */
    public function __construct($partner_id, $base_url = null, ClientInterface $client = null)
    {
        if (null === $base_url) {
            $base_url = 'https://www.mollie.nl/xml/';
        }
        parent::__construct($base_url, $client);
        $this->setPartnerId($partner_id);
    }

    /**
     * @return int Mollie Account Number
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * @param int $partnerId Mollie Account Number
     */
    public function setPartnerId($partnerId)
    {
        if (!filter_var($partnerId, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Partner ID (Mollie account number) should be a number.');
        }
        $this->partnerId = intval($partnerId);
    }

    /**
     * @return string Mollie Payment Profile key
     */
    public function getProfileKey()
    {
        return $this->profileKey;
    }

    /**
     * @param string $profileKey Mollie Payment Profile key
     */
    public function setProfileKey($profileKey)
    {
        $this->profileKey = $profileKey;
    }

    /**
     *
     * @param int|string $amount Amount in cents or 'endless'
     * @param string $report_url Reporting URL (optional)
     * @param int $country Country code (optional)
     * @param string $service_number Service number (optional)
     * @return AMNL\Mollie\IVR\MicropaymentResponse
     */
    public function preparePayment($amount, $report_url = null, $country = null, $service_number = null)
    {
        // Check arguments
        if ($amount != 'endless' && !filter_var($amount, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('Amount should be a number (eurocents) or "endless".');
        }
        if ($report_url != null && !filter_var($report_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Report URL should be a valid URL when supplied (argument is optional).');
        }
        if ($country != null && !in_array($country, $this->getSupportedCountryCodes())) {
            throw new \InvalidArgumentException('Supplied country code is not supported (argument is optional).');
        }

        // Convert amount
        if ($amount != 'endless') {
            $amount = number_format(intval($amount) / 100, 2, '.', '');
        }

        // Params
        $params = array(
            'a' => 'fetch',
            'partnerid' => $this->partnerId,
            'amount' => $amount,
            'report' => $report_url,
            'country' => $country,
            'servicenumber' => $service_number,
        );

        // Do request
        $xml = $this->request('micropayment', $params);

        // Gather data
        if (null === $xml->item) {
            // TODO Add fancy error message
            throw new MollieException();
        }

        // Fields that should always be present according to the docs
        $countryCode = intval($xml->item['country']);
        $serviceNumber = (string) $xml->item->servicenumber;
        $payCode = (string) $xml->item->paycode;
        $rawPayOut = (float) $xml->item->payout;

        // Mode
        $mode = strtolower(trim((string) $xml->item->mode));
        if ($mode == self::MODE_PAYPERCALL) {
            $rawRate = (float) $xml->item->costpercall;
        } elseif ($mode == self::MODE_PAYPERMINUTE) {
            $rawRate = (float) $xml->item->costperminute;
        } else {
            throw new MollieException('Unknown mode returned by server.');
        }

        // Transform rate, payout and amount
        $rate = (int) round($rawRate * 100);
        $payOut = (int) round($rawPayOut * 100);
        $amount = null;
        if ($xml->item->amount != null) {
            $amount = (int) round(((float) $xml->item->amount) * 100);
        }

        // Duration
        $duration = null;
        if ($mode == self::MODE_PAYPERMINUTE && $amount != null && $xml->item->duration != null) {
            $duration = (int) $xml->item->duration;
        }

        // Return MicropaymentResponse
        return new MicropaymentResponse($serviceNumber, $payCode, $mode, $rate, $payOut, $amount, $duration);
    }

    /**
     *
     * @param string $service_number Service Number
     * @param string $pay_code Payment Code
     * @return AMNL\Mollie\IVR\MicropaymentStatus
     */
    public function checkPayment($service_number, $pay_code)
    {
        // Params
        $params = array(
            'a' => 'check',
            'servicenumber' => trim($service_number),
            'paycode' => trim($pay_code),
        );

        // Do request
        $xml = $this->request('micropayment', $params);

        // Gather data
        if (null === $xml->item) {
            // TODO Add fancy error message
            throw new MollieException();
        }

        // Fields that are always present according to the docs
        $serviceNumber = (string) $xml->item->servicenumber;
        $payCode = (string) $xml->item->paycode;
        $mode = (string) $xml->item->mode;
        $rawAmount = (float) $xml->item->amount;
        $message = (string) $xml->item->paystatus;

        // Payment unknown?
        if ($message == 'Payment unknown.' || empty($payCode)) {
            throw new MollieException($message);
        }

        // Optional fields
        $paid = false;
        if ($xml->item->payed != null && ((string) $xml->item->payed) == 'true') {
            $paid = true;
        }
        $paying = false;
        if ($xml->item->paying != null && ((string) $xml->item->paying) == 'true') {
            $paying = true;
        }
        $durationLeft = null;
        if ($xml->item->durationleft != null) {
            $durationLeft = (int) $xml->item->durationleft;
        }
        $durationDone = null;
        if ($xml->item->durationdone != null) {
            $durationDone = (int) $xml->item->durationdone;
        }

        // Transform amount
        $amount = (int) round($rawAmount * 100);

        // Return MicropaymenStatus
        return new MicropaymentStatus($serviceNumber, $payCode, $mode, $amount, $message, $paid, $paying, $durationDone, $durationLeft);
    }

    /**
     * @return array List of supported country codes
     */
    public function getSupportedCountryCodes()
    {
        return array(31, 32, 33, 34, 39, 41, 43, 44, 49);
    }

}
