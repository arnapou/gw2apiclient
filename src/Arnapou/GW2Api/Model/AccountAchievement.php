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
 * @method string getId()
 * @method string getMax()
 * @method string getCurrent()
 * @method string getBits()
 * @method string getRepeated()
 */
class AccountAchievement extends AbstractObject
{
    protected $total = null;
    protected $achievement;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['id'])) {
            $this->achievement = new Achievement($this->getEnvironment(), $data['id']);
        }
    }

    public function isDone()
    {
        return $this->getData('done') ? true : false;
    }

    /**
     *
     * @return int
     */
    public function getTotalAP()
    {
        if ($this->total === null) {
            $this->total = 0;
            $repeated    = $this->getRepeated();
            $pointCap    = $this->getAchievement()->getPointCap();
            $tiers       = $this->getAchievement()->getTiers();
            if ($repeated) {
                foreach ($tiers as $tier) {
                    if (isset($tier['points'])) {
                        $this->total += $repeated * $tier['points'];
                    }
                }
                $current = $this->getCurrent();
                foreach ($tiers as $tier) {
                    if (isset($tier['points'], $tier['count']) && $current >= $tier['count']) {
                        $this->total += $tier['points'];
                    }
                }
            } elseif ($this->isDone()) {
                foreach ($tiers as $tier) {
                    if (isset($tier['points'])) {
                        $this->total += $tier['points'];
                    }
                }
            } else {
                $current = $this->getCurrent();
                foreach ($tiers as $tier) {
                    if (isset($tier['points'], $tier['count']) && $current >= $tier['count']) {
                        $this->total += $tier['points'];
                    }
                }
            }
            if ($pointCap && $this->total > $pointCap) {
                $this->total = $pointCap;
            }
            if ($this->total < 0) {
                $this->total = 0;
            }
        }
        return $this->total;
    }

    /**
     *
     * @return Achievement
     */
    public function getAchievement()
    {
        return $this->achievement;
    }
}
