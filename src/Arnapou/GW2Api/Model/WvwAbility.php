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
 * @method string getDescription()
 * @method string getIcon()
 */
class WvwAbility extends AbstractStoredObject
{

    protected $rank = 0;

    public function getRanks()
    {
        return $this->getData('ranks', []);
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function getTotalCost()
    {
        $cost = 0;
        foreach ($this->getRanks() as $i => $rank) {
            if ($this->rank >= $i + 1 && isset($rank['cost'])) {
                $cost += $rank['cost'];
            }
        }
        return $cost;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    public function getApiName()
    {
        return 'wvwabilities';
    }
}
