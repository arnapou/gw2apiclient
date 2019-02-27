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
 * @method string getTeam()
 * @method string getResult()
 * @method string getProfession()
 */
class PvpGame extends AbstractObject
{
    // results
    const RESULT_VICTORY = 'Victory';
    const RESULT_DEFEAT  = 'Defeat';
    // teams
    const TEAM_RED  = 'Red';
    const TEAM_BLUE = 'Blue';

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var Map
     */
    protected $map;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['map_id'])) {
            $this->map = new Map($this->getEnvironment(), $data['map_id']);
        }
    }

    /**
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     *
     * @return string
     */
    public function isVictory()
    {
        return $this->getData('result') == self::RESULT_VICTORY;
    }

    /**
     *
     * @return string
     */
    public function getDateStarted()
    {
        return gmdate('Y-m-d H:i:s', strtotime($this->getData('started')));
    }

    /**
     *
     * @return string
     */
    public function getDateEnded()
    {
        return gmdate('Y-m-d H:i:s', strtotime($this->getData('ended')));
    }

    /**
     *
     * @return string
     */
    public function getDuration()
    {
        return strtotime($this->getData('ended')) - strtotime($this->getData('started'));
    }

    /**
     *
     * @return string
     */
    public function getScoreBlue()
    {
        return $this->getData(['scores', 'blue']);
    }

    /**
     *
     * @return string
     */
    public function getScoreRed()
    {
        return $this->getData(['scores', 'red']);
    }
}
