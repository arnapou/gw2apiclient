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
 * @doc https://wiki.guildwars2.com/wiki/API:2/itemstats
 * 
 * @method string getName()
 */
class ItemStat extends AbstractStoredObject
{

    // ATTRIBUTES
    const ATTRIBUTE_AGONY_RESISTANCE   = 'AgonyResistance';
    const ATTRIBUTE_BOON_DURATION      = 'BoonDuration';
    const ATTRIBUTE_CONDITION_DAMAGE   = 'ConditionDamage';
    const ATTRIBUTE_CONDITION_DURATION = 'ConditionDuration';
    const ATTRIBUTE_CRIT_DAMAGE        = 'CritDamage';
    const ATTRIBUTE_HEALING            = 'Healing';
    const ATTRIBUTE_POWER              = 'Power';
    const ATTRIBUTE_PRECISION          = 'Precision';
    const ATTRIBUTE_THOUGHNESS         = 'Toughness';
    const ATTRIBUTE_VITALITY           = 'Vitality';

    /**
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->getData('attributes', []);
    }

    /**
     * 
     * @return string
     */
    public function getStatName()
    {
        return \Arnapou\GW2Api\attributes_to_statname($this->getAttributes());
    }

    public function getApiName()
    {
        return 'itemstats';
    }

    public function __toString()
    {
        return $this->getStatName();
    }
}
