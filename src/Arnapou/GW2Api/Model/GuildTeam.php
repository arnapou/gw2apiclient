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
 * @method string getName()
 * @method string getState)
 */
class GuildTeam extends AbstractObject
{

    /**
     *
     * @var array
     */
    protected $members = [];

    /**
     *
     * @var array
     */
    protected $games = [];

    /**
     *
     * @var array
     */
    protected $ladders = [];

    /**
     *
     * @var PvpStats
     */
    protected $aggregate;

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();
        if (isset($data['members']) && is_array($data['members'])) {
            foreach ($data['members'] as $item) {
                $this->members[] = new GuildTeamMember($env, $item);
            }
            usort($this->members, function ($a, $b) {
                return strcasecmp($a->getRole() . ':' . $a->getName(), $b->getRole() . ':' . $b->getName());
            });
        }
        if (isset($data['aggregate'])) {
            $this->aggregate = new PvpStats($env, $data['aggregate']);
        }
        if (isset($data['games']) && is_array($data['games'])) {
            foreach ($data['games'] as $item) {
                $this->games[] = new PvpGame($env, $item);
            }
            uasort($this->games, function ($a, $b) {
                return -strcmp($a->getDateEnded(), $b->getDateEnded());
            });
        }
        if (isset($data['ladders']) && is_array($data['ladders'])) {
            $this->ladders = [];
            foreach ($data['ladders'] as $key => $ladder) {
                $this->ladders[$key] = new PvpStats($this->getEnvironment(), $ladder);
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     *
     * @return PvpStats
     */
    public function getAggregateStats()
    {
        return $this->aggregate;
    }

    /**
     *
     * @return array
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     *
     * @param string $key
     * @return array
     */
    public function getLadderStats($key)
    {
        if (!array_key_exists($key, $this->ladders)) {
            return null;
        }
        return $this->ladders[$key];
    }

    /**
     *
     * @return PvpStats
     */
    public function getLadderRanked()
    {
        return $this->getLadderStats('ranked');
    }

    /**
     *
     * @return PvpStats
     */
    public function getLadderUnranked()
    {
        return $this->getLadderStats('unranked');
    }
}
