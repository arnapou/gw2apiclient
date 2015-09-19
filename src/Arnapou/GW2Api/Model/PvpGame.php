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
class PvpGame extends AbstractObject {

    // results
    const RESULT_VICTORY = 'Victory';
    const RESULT_DEFEAT  = 'Defeat';
    // teams
    const TEAM_RED       = 'Red';
    const TEAM_BLUE      = 'Blue';

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

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
    }

    /**
     * 
     * @return Map
     */
    public function getMap() {
        if (!isset($this->map)) {
            $this->map = new Map($this->client, $this->data['map_id']);
        }
        return $this->map;
    }

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * 
     * @return string
     */
    public function getTeam() {
        return $this->data['team'];
    }

    /**
     * 
     * @return string
     */
    public function isVictory() {
        return $this->data['result'] == self::RESULT_VICTORY;
    }

    /**
     * 
     * @return string
     */
    public function getResult() {
        return $this->data['result'];
    }

    /**
     * 
     * @return string
     */
    public function getDateStarted() {
        return gmdate('Y-m-d H:i:s', strtotime($this->data['started']));
    }

    /**
     * 
     * @return string
     */
    public function getDateEnded() {
        return gmdate('Y-m-d H:i:s', strtotime($this->data['ended']));
    }

    /**
     * 
     * @return string
     */
    public function getDuration() {
        return strtotime($this->data['ended']) - strtotime($this->data['started']);
    }

    /**
     * 
     * @return string
     */
    public function getScoreBlue() {
        return $this->data['scores']['blue'];
    }

    /**
     * 
     * @return string
     */
    public function getScoreRed() {
        return $this->data['scores']['red'];
    }

    /**
     * 
     * @return string
     */
    public function getProfession() {
        return $this->data['profession'];
    }

    /**
     * 
     * @return string
     */
    public function getProfessionIcon() {
        return $this->apiIcon('icon_' . strtolower($this->getProfession()));
    }

    /**
     * 
     * @return string
     */
    public function getProfessionIconBig() {
        return $this->apiIcon('icon_' . strtolower($this->getProfession()) . '_big');
    }

}
