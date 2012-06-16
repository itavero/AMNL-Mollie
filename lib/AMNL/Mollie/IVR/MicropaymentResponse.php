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

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class MicropaymentResponse
{

    /**
     * @var string Service Number that should be called by the Customer
     */
    protected $serviceNumber;

    /**
     * @var string Payment Code that should be entered by the Customer
     */
    protected $payCode;

    /**
     * @var string Mode; can be compared with the constants prefixed with MODE_
     */
    protected $mode;

    /**
     * @var int Rate in cents (either per minute or per call depending on the Mode)
     */
    protected $rate;

    /**
     * @var int Pay-out in cents (amount of money that will be deposited on your Mollie account)
     */
    protected $payOut;

    /**
     * @var int Amount (in cents) if you have supplied it.
     */
    protected $amount;

    /**
     * @var int Duration in seconds (if you have supplied an amount and the mode is Pay per Call)
     */
    protected $duration;

    public function __construct($service_number, $pay_code, $mode, $rate, $pay_out = null, $amount = null, $duration = null)
    {
        $this->serviceNumber = $service_number;
        $this->payCode = $pay_code;
        $this->mode = $mode;
        $this->rate = $rate;
        $this->payOut = $pay_out;
        $this->amount = $amount;
        $this->duration = $duration;
    }

    /**
     * @return boolean True if the mode is Pay-Per-Call
     */
    public function isPayPerCall()
    {
        return ($this->mode === MicropaymentGateway::MODE_PAYPERCALL);
    }

    /**
     * @return boolean True if the mode is Pay-Per-Minute
     */
    public function isPayPerMinute()
    {
        return ($this->mode === MicropaymentGateway::MODE_PAYPERMINUTE);
    }

    /**
     * @return string Service Number that should be called by the Customer
     */
    public function getServiceNumber()
    {
        return $this->serviceNumber;
    }

    /**
     * @return string Payment Code that should be entered by the Customer
     */
    public function getPayCode()
    {
        return $this->payCode;
    }

    /**
     * @return string Mode; can be compared with the constants prefixed with MODE_
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return int Rate in cents (either per minute or per call depending on the Mode)
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return int Amount (in cents) if you have supplied it.
     */
    public function getPayOut()
    {
        return $this->payOut;
    }

    /**
     * @return int Amount (in cents) if you have supplied it.
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int Duration in seconds (if you have supplied an amount and the mode is Pay per Call)
     */
    public function getDuration()
    {
        return $this->duration;
    }

}
