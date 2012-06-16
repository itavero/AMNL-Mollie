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
class MicropaymentStatus
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
     * @var int Amount (in cents)
     */
    protected $amount;

    /**
     * @var string Message that describes the current state of the payment
     */
    protected $message;

    /**
     * @var boolean True if the payment has been finished
     */
    protected $paymentDone;

    /**
     * @var boolean True if the payment is in progress (only available when amount='endless')
     */
    protected $paymentBusy;

    /**
     * @var int Number of seconds the call has already lasted
     */
    protected $durationDone;

    /**
     * @var int Number of seconds left before the payment is finished
     */
    protected $durationLeft;

    public function __construct($service_number, $pay_code, $mode, $amount, $message, $paid = false, $paying = false, $duration_done = null, $duration_left = null)
    {
        $this->serviceNumber = $service_number;
        $this->payCode = $pay_code;
        $this->mode = $mode;
        $this->amount = $amount;
        $this->message = $message;
        $this->paymentDone = $paid;
        $this->paymentBusy = $paying;
        $this->durationDone = $duration_done;
        $this->durationLeft = $duration_left;
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
     * @return int Amount (in cents)
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string Message that describes the current state of the payment
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return boolean True if the payment has been finished
     */
    public function isDone()
    {
        return $this->paymentDone;
    }

    /**
     * @return boolean True if the payment is in progress (only available when amount='endless')
     */
    public function isBusy()
    {
        return $this->paymentBusy;
    }

    /**
     * @return int Number of seconds the call has already lasted
     */
    public function getDurationDone()
    {
        return $this->durationDone;
    }

    /**
     * @return int Number of seconds left before the payment is finished
     */
    public function getDurationLeft()
    {
        return $this->durationLeft;
    }

}
