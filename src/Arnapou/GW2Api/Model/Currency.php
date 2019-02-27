<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Model;

/**
 * @doc https://wiki.guildwars2.com/wiki/API:2/worlds
 *
 * @method string getName()
 * @method string getDescription()
 * @method string getOrder()
 * @method string getIcon()
 */
class Currency extends AbstractStoredObject
{
    private $quantity = 0;

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function getApiName()
    {
        return 'currencies';
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}
