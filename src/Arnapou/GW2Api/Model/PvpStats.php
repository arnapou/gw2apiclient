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
 * @method string getWins()
 * @method string getLosses()
 * @method string getDesertions()
 * @method string getByes()
 * @method string getForfeits()
 */
class PvpStats extends AbstractObject
{
    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var int
     */
    protected $total;

    /**
     *
     * @return float
     */
    public function getWinRate()
    {
        $wins   = $this->getWins();
        $losses = $this->getLosses();
        if ($losses + $wins > 0) {
            return round(100 * $wins / ($losses + $wins), 2);
        }
        return null;
    }

    /**
     *
     * @return int
     */
    public function getTotal()
    {
        if (!isset($this->total)) {
            $this->total = $this->getWins() + $this->getLosses() + $this->getDesertions() + $this->getByes() + $this->getForfeits();
        }
        return $this->total;
    }
}
