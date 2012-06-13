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
interface ProviderGateway
{

    /**
     * @param int $partner_id Mollie.nl account number
     */
    public function setPartnerId($partner_id);

    /**
     * @return int Mollie.nl account number
     */
    public function getPartnerId();

    /**
     * @param string $profile_key Mollie.nl payment profile ID 
     */
    public function setProfileKey($profile_key);

    /**
     * @return string Mollie.nl payment profile ID
     */
    public function getProfileKey();

    /**
     * Prepare / create a transaction
     * 
     * @return ProviderResponse
     */
    public function preparePayment($amount, $report_url, $return_url, $description, array $options = null);

    /**
     * Check the status of a transaction
     *
     * @return TransactionStatus 
     */
    public function checkPayment($transaction_id);
}
