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

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Pvp extends AbstractObject {

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
     * @param SimpleClient $client
     */
    public function __construct(SimpleClient $client) {
        parent::__construct($client);
    }

    /**
     * 
     */
    protected function checkStats() {
        if (!isset($this->data)) {
            $this->data = $this->client->v2_pvp_stats();
        }
    }

    /**
     * 
     * @return PvpStats
     */
    public function getAggregateStats() {
        if (!isset($this->aggregate)) {
            $this->checkStats();
            $this->aggregate = new PvpStats($this->client, $this->data['aggregate']);
        }
        return $this->aggregate;
    }

    /**
     * 
     * @param string $key
     * @return array
     */
    public function getLadderStats($key) {
        if (!array_key_exists($key, $this->ladders)) {
            $this->checkStats();
            if (isset($this->data['ladders'], $this->data['ladders'][$key])) {
                $this->ladders[$key] = new PvpStats($this->client, $this->data['ladders'][$key]);
            }
            else {
                $this->ladders[$key] = null;
            }
        }
        return $this->ladders[$key];
    }

    /**
     * 
     * @return array
     */
    public function getLadderNone() {
        return $this->getLadderStats('none');
    }

    /**
     * 
     * @return array
     */
    public function getLadderRanked() {
        return $this->getLadderStats('ranked');
    }

    /**
     * 
     * @return array
     */
    public function getLadderUnranked() {
        return $this->getLadderStats('unranked');
    }

    /**
     * 
     * @return array
     */
    public function getLadderSoloArenaRated() {
        return $this->getLadderStats('soloarenarated');
    }

    /**
     * 
     * @return array
     */
    public function getLadderTeamArenaRated() {
        return $this->getLadderStats('teamarenarated');
    }

    /**
     * 
     * @return PvpStats
     */
    public function getProfessionsStats() {
        if (!isset($this->professions)) {
            $this->checkStats();
            $this->professions = [];
            if (isset($this->data['professions'])) {
                foreach ($this->data['professions'] as $profession => $data) {
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
                    $this->professions[$data['profession']] = new PvpStatsProfession($this->client, $data);
                }
            }
            uasort($this->professions, function($a, $b) {
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
    public function getGames() {
        if (!isset($this->games)) {
            $games       = $this->client->v2_pvp_games($this->client->v2_pvp_games());
            $this->games = [];
            foreach ($games as $game) {
                self::$PRELOADS['maps'][] = $game['map_id'];
            }
            foreach ($games as $game) {
                $this->games[] = new PvpGame($this->client, $game);
            }
            uasort($this->games, function($a, $b) {
                return -strcmp($a->getDateEnded(), $a->getDateStarted());
            });
        }
        return $this->games;
    }

    /**
     * 
     * @return integer
     */
    public function getRank() {
        $this->checkStats();
        return $this->data['pvp_rank'];
    }

    /**
     * 
     * @return string
     */
    public function getRankName() {
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
