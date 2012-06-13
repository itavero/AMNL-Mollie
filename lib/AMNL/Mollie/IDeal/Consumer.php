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

/**
 * 
 *
 * @author Arno Moonen <info@arnom.nl>
 */
class Consumer
{

    /**
     * @var string Name
     */
    protected $name;

    /**
     * @var string Bank Account
     */
    protected $accountNumber;

    /**
     * @var string City
     */
    protected $city;

    /**
     *
     * @param string $name Name
     * @param string $account_number Bank Account
     * @param string $city City
     */
    public function __construct($name, $account_number, $city)
    {
        $this->name = $name;
        $this->accountNumber = $account_number;
        $this->city = $city;
    }

    /**
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Bank Account
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @return string City
     */
    public function getCity()
    {
        return $this->city;
    }

}
