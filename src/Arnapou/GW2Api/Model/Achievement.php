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
 * @method string getDescription()
 * @method string getIcon()
 * @method string getName()
 * @method string getLockedText()
 * @method string getRequirement()
 * @method string getPointCap()
 * @method string getType()
 */
class Achievement extends AbstractStoredObject {

    // TYPES
    const TYPE_DEFAULT                = 'Default';
    const TYPE_ITEMSET                = 'ItemSet';
    // FLAGS
    const FLAG_PVP                    = 'Pvp';
    const FLAG_CATEGORY_DISPLAY       = 'CategoryDisplay';
    const FLAG_MOVE_TO_TOP            = 'MoveToTop';
    const FLAG_IGNORE_NEARLY_COMPLETE = 'IgnoreNearlyComplete';
    // BITS TYPE
    const BITS_TYPE_TEXT              = 'Text';
    const BITS_TYPE_ITEM              = 'Item';
    const BITS_TYPE_MINIPET           = 'Minipet';
    const BITS_TYPE_SKIN              = 'Skin';
    // REWARDS TYPE
    const REWARDS_TYPE_TEXT           = 'Coins';
    const REWARDS_TYPE_ITEM           = 'Item';
    const REWARDS_TYPE_MASTERY        = 'Mastery';

    /**
     * 
     * @return array
     */
    public function getBits() {
        return $this->getData('bits', []);
    }

    /**
     * 
     * @return array
     */
    public function getFlags() {
        return $this->getData('flags', []);
    }

    /**
     * 
     * @return array
     */
    public function getTiers() {
        return $this->getData('tiers', []);
    }

    /**
     * 
     * @return array
     */
    public function getRewards() {
        return $this->getData('rewards', []);
    }

    /**
     * 
     * @param string $flag
     * @return boolean
     */
    public function hasFlag($flag) {
        return in_array($flag, (array) $this->getFlags());
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    public function getApiName() {
        return 'achievements';
    }

}
