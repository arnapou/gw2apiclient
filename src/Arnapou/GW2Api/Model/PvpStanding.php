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
 * @method string getBest()
 * @method string getCurrent()
 * @method string getSeasonId()
 */
class PvpStanding extends AbstractObject
{

    protected $season;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['season_id'])) {
            $this->season = new PvpSeason($this->getEnvironment(), $data['season_id']);
        }
    }

    public function getBestTotalPoints()
    {
        return $this->getData(['best', 'total_points']);
    }

    public function getBestDivision()
    {
        return $this->getData(['best', 'division']);
    }

    public function getBestTier()
    {
        return $this->getData(['best', 'tier']);
    }

    public function getBestPoints()
    {
        return $this->getData(['best', 'points']);
    }

    public function getBestRepeats()
    {
        return $this->getData(['best', 'repeats']);
    }

    public function getCurrentTotalPoints()
    {
        return $this->getData(['current', 'total_points']);
    }

    public function getCurrentDivision()
    {
        return $this->getData(['current', 'division']);
    }

    public function getCurrentTier()
    {
        return $this->getData(['current', 'tier']);
    }

    public function getCurrentPoints()
    {
        return $this->getData(['current', 'points']);
    }

    public function getCurrentRepeats()
    {
        return $this->getData(['current', 'repeats']);
    }

    public function getCurrentRating()
    {
        return $this->getData(['current', 'rating']);
    }

    public function getCurrentDecay()
    {
        return $this->getData(['current', 'decay']);
    }

    /**
     *
     * @return PvpSeason
     */
    public function getSeason()
    {
        return $this->season;
    }
}
