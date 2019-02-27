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
class Achievement extends AbstractStoredObject
{
    // TYPES
    const TYPE_DEFAULT = 'Default';
    const TYPE_ITEMSET = 'ItemSet';
    // FLAGS
    const FLAG_PVP                    = 'Pvp';
    const FLAG_CATEGORY_DISPLAY       = 'CategoryDisplay';
    const FLAG_MOVE_TO_TOP            = 'MoveToTop';
    const FLAG_IGNORE_NEARLY_COMPLETE = 'IgnoreNearlyComplete';
    const FLAG_REPEATABLE             = 'Repeatable';
    // BITS TYPE
    const BITS_TYPE_TEXT    = 'Text';
    const BITS_TYPE_ITEM    = 'Item';
    const BITS_TYPE_MINIPET = 'Minipet';
    const BITS_TYPE_SKIN    = 'Skin';
    // REWARDS TYPE
    const REWARDS_TYPE_TEXT    = 'Coins';
    const REWARDS_TYPE_ITEM    = 'Item';
    const REWARDS_TYPE_MASTERY = 'Mastery';
    const REWARDS_TYPE_TITLE   = 'Title';

    protected $total   = null;
    protected $rewards = [];
    protected $bits    = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['rewards']) && \is_array($data['rewards'])) {
            $env = $this->getEnvironment();
            foreach ($data['rewards'] as $item) {
                if (isset($item['type'], $item['id'])) {
                    if (self::REWARDS_TYPE_ITEM == $item['type']) {
                        $item = $item + ['object' => new Item($env, $item['id'])];
                    } elseif (self::REWARDS_TYPE_TITLE == $item['type']) {
                        $item = $item + ['object' => new Title($env, $item['id'])];
                    }
                }
                $this->rewards[] = $item;
            }
        }

        if (isset($data['bits']) && \is_array($data['bits'])) {
            $env = $this->getEnvironment();
            foreach ($data['bits'] as $item) {
                if (isset($item['type'], $item['id'])) {
                    if (self::BITS_TYPE_ITEM == $item['type']) {
                        $item = $item + ['object' => new Item($env, $item['id'])];
                    } elseif (self::BITS_TYPE_MINIPET == $item['type']) {
                        $item = $item + ['object' => new Mini($env, $item['id'])];
                    } elseif (self::BITS_TYPE_SKIN == $item['type']) {
                        $item = $item + ['object' => new Skin($env, $item['id'])];
                    }
                }
                $this->bits[] = $item;
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getBits()
    {
        return $this->bits;
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
     * @return array
     */
    public function getTiers()
    {
        return $this->getData('tiers', []);
    }

    /**
     *
     * @return int
     */
    public function getTotalAP()
    {
        if ($this->total === null) {
            $this->total = 0;
            $pointCap    = $this->getPointCap();
            if ($pointCap) {
                $this->total = $pointCap;
            } else {
                foreach ($this->getTiers() as $tier) {
                    if (isset($tier['points'])) {
                        $this->total += $tier['points'];
                    }
                }
            }
            if ($this->total < 0) {
                $this->total = 0;
            }
        }
        return $this->total;
    }

    /**
     *
     * @return array
     */
    public function getRewards()
    {
        return $this->rewards;
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return \in_array($flag, (array)$this->getFlags());
    }

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
        return 'achievements';
    }
}
