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
 *
 * @method string getName()
 * @method string getLargeIcon()
 * @method string getSmallIcon()
 * @method string getPipIcon()
 */
class PvpDivision extends AbstractObject
{

    // results
    const FLAG_CAN_LOSE_POINTS = 'CanLosePoints';
    const FLAG_CAN_LOSE_TIERS  = 'CanLoseTiers';
    const FLAG_REPEATABLE      = 'Repeatable';

    /**
     *
     * @return array
     */
    public function getTiers()
    {
        return $this->getData('tiers', []);
    }

    /**
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->getData('flags', []);
    }

    /**
     *
     * @param string $flag
     * @return boolean
     */
    public function hasFlag($flag)
    {
        return in_array($flag, (array)$this->getFlags());
    }
}
