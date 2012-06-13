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
class Bank
{

    /**
     * @var int Bank ID
     */
    protected $id;

    /**
     * @var string Bank Name
     */
    protected $name;

    /**
     *
     * @param int $id Bank ID
     * @param string $name Bank Name
     */
    public function __construct($id, $name)
    {
        $this->id = intval($id);
        $this->name = strval($name);
    }

    /**
     * @return string Bank ID
     */
    public function getId()
    {
        return str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     *
     * @return string Bank Name
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

}
