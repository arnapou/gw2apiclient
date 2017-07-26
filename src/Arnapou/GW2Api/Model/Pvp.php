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
 */
class Pvp extends AbstractObject
{

    const RANK_RABBIT  = 'Rabbit';
    const RANK_DEER    = 'Deer';
    const RANK_DOLYAK  = 'Dolyak';
    const RANK_WOLF    = 'Wolf';
    const RANK_TIGER   = 'Tiger';
    const RANK_BEAR    = 'Bear';
    const RANK_SHARK   = 'Shark';
    const RANK_PHOENIX = 'Phoenix';
    const RANK_DRAGON  = 'Dragon';

    /**
     *
     * @var array
     */
    protected $games;

    /**
     *
     * @var array
     */
    protected $stats;

    /**
     *
     * @var PvpStats
     */
    protected $aggregate;

    /**
     *
     * @var array
     */
    protected $professions;

    /**
     *
     * @var array
     */
    protected $ladders = [];

    /**
     *
     * @var array
     */
    protected $standings;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['aggregate'])) {
            $this->aggregate = new PvpStats($this->getEnvironment(), $data['aggregate']);
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
    public function getStandings()
    {
        if ($this->standings === null) {
            $this->standings = [];
            $env             = $this->getEnvironment();
            $data            = $env->getClientVersion2()->apiPvpStandings();
            if (is_array($data)) {
                foreach ($data as $item) {
                    $this->standings[] = new PvpStanding($env, $item);
                }
            }
            usort($this->standings, function (PvpStanding $a, PvpStanding $b) {
                $sa = $a->getSeason();
                $sb = $b->getSeason();
                $d1 = (string)($sa ? $sa->getDateStart() : '');
                $d2 = (string)($sb ? $sb->getDateStart() : '');
                return -strcmp($d1, $d2);
            });
        }
        return $this->standings;
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
    public function getLadderNone()
    {
        return $this->getLadderStats('none');
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

    /**
     *
     * @return PvpStats
     */
    public function getLadderSoloArenaRated()
    {
        return $this->getLadderStats('soloarenarated');
    }

    /**
     *
     * @return PvpStats
     */
    public function getLadderTeamArenaRated()
    {
        return $this->getLadderStats('teamarenarated');
    }

    /**
     *
     * @return PvpStats
     */
    public function getProfessionsStats()
    {
        if (!isset($this->professions)) {
            $professions       = $this->getData('professions');
            $this->professions = [];
            if (!empty($professions) && is_array($professions)) {
                $env = $this->getEnvironment();
                foreach ($professions as $profession => $data) {
                    $data['profession']                     = [
                        'elementalist' => Character::PROFESSION_ELEMENTALIST,
                        'engineer'     => Character::PROFESSION_ENGINEER,
                        'guardian'     => Character::PROFESSION_GUARDIAN,
                        'mesmer'       => Character::PROFESSION_MESMER,
                        'necromancer'  => Character::PROFESSION_NECROMANCER,
                        'ranger'       => Character::PROFESSION_RANGER,
                        'thief'        => Character::PROFESSION_THIEF,
                        'elementalist' => Character::PROFESSION_ELEMENTALIST,
                        'warrior'      => Character::PROFESSION_WARRIOR,
                        'revenant'     => Character::PROFESSION_REVENANT,
                    ][strtolower($profession)];
                    $this->professions[$data['profession']] = new PvpStatsProfession($env, $data);
                }
            }
            uasort($this->professions, function ($a, $b) {
                $v1 = $a->getTotal();
                $v2 = $b->getTotal();
                if ($v1 == $v2) {
                    return 0;
                }
                return $v1 < $v2 ? 1 : -1;
            });
        }
        return $this->professions;
    }

    /**
     *
     * @return array
     */
    public function getGames()
    {
        if (!isset($this->games)) {
            $env         = $this->getEnvironment();
            $games       = $env->getClientVersion2()->apiPvpGames($env->getClientVersion2()->apiPvpGames());
            $this->games = [];
            foreach ($games as $game) {
                $this->games[] = new PvpGame($env, $game);
            }
            uasort($this->games, function ($a, $b) {
                return -strcmp($a->getDateEnded(), $b->getDateEnded());
            });
        }
        return $this->games;
    }

    /**
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->getData('pvp_rank');
    }

    /**
     *
     * @return integer
     */
    public function getRankPoints()
    {
        return $this->getData('pvp_rank_points');
    }

    /**
     *
     * @return integer
     */
    public function getRankRollovers()
    {
        return $this->getData('pvp_rank_rollovers');
    }

    /**
     *
     * @return string
     */
    public function getRankName()
    {
        return [
            self::RANK_RABBIT,
            self::RANK_DEER,
            self::RANK_DOLYAK,
            self::RANK_WOLF,
            self::RANK_TIGER,
            self::RANK_BEAR,
            self::RANK_SHARK,
            self::RANK_PHOENIX,
            self::RANK_DRAGON,
        ][floor($this->getRank() / 10)];
    }
}
