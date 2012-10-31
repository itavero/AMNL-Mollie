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

use Buzz\Client\ClientInterface;

/**
 *
 *
 * @author Arno Moonen <info@arnom.nl>
 */
abstract class BaseGateway extends Client implements ProviderGateway
{

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
     * @param string $profile_key Mollie Payment Profile key
     * @param string $base_url Webservice API base. The path will be appended to this.
     * @param ClientInterface $client Client to be used by the Browser instance
     */
    public function __construct($partner_id, $profile_key = null, $base_url, ClientInterface $client = null)
    {
        parent::__construct($base_url, $client);

        // Set properties
        $this->setPartnerId($partner_id);
        $this->profileKey = $profile_key;
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

}
