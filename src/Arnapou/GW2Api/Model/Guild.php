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
 * @method string getTag()
 * @method array getEmblem()
 */
class Guild extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $log = [];

    /**
     *
     * @var array
     */
    protected $stash = [];

    /**
     *
     * @var array
     */
    protected $members = [];

    /**
     *
     * @var array
     */
    protected $ranks = [];

    /**
     *
     * @var array
     */
    protected $treasury = [];

    /**
     *
     * @var array
     */
    protected $teams = [];

    /**
     *
     * @var array
     */
    protected $upgrades = [];

    /**
     *
     * @var array
     */
    protected $stashprice;

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->getData('guild_id');
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getData('guild_name');
    }

    /**
     * 
     * @return boolean
     */
    public function isLeader() {
        return $this->getData('x-leader', false);
    }

    /**
     * 
     * @return boolean
     */
    public function hasEmblem() {
        $emblem = $this->getEmblem();
        if (!is_array($emblem) || empty($emblem)) {
            return false;
        }
        return true;
    }

    /**
     * 
     * @return array
     */
    public function getLog() {
        if (empty($this->log) && $this->isLeader()) {
            $env  = $this->getEnvironment();
            $data = $env->getClientVersion2()->apiGuildLog($this->getId());
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $obj                        = new GuildLog($env, $item);
                    $this->ranks[$obj->getId()] = $obj;
                }
            }
        }
        return $this->log;
    }

    /**
     * 
     * @return array
     */
    public function getMembers() {
        if (empty($this->members) && $this->isLeader()) {
            $env  = $this->getEnvironment();
            $data = $env->getClientVersion2()->apiGuildMembers($this->getId());
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $obj                        = new GuildMember($env, $item);
                    $this->ranks[$obj->getId()] = $obj;
                }
                uasort($this->ranks, function($a, $b) {
                    $sa = (string) $a->getName();
                    $sb = (string) $b->getName();
                    return strcmp($sa, $sb);
                });
            }
        }
        return $this->members;
    }

    /**
     * 
     * @return array
     */
    public function getRanks() {
        if (empty($this->ranks) && $this->isLeader()) {
            $env  = $this->getEnvironment();
            $data = $env->getClientVersion2()->apiGuildRanks($this->getId());
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $obj                        = new GuildRank($env, $item);
                    $this->ranks[$obj->getId()] = $obj;
                }
                uasort($this->ranks, function($a, $b) {
                    $na = $a->getOrder();
                    $nb = $b->getOrder();
                    if ($na == $nb) {
                        return 0;
                    }
                    return $na > $nb ? 1 : -1;
                });
            }
        }
        return $this->ranks;
    }

    /**
     * 
     * @return array
     */
    public function getStash() {
        if (empty($this->stash) && $this->isLeader()) {
            $env  = $this->getEnvironment();
            $data = $env->getClientVersion2()->apiGuildStash($this->getId());
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $this->stash[] = new GuildStash($env, $item);
                }
            }
        }
        return $this->stash;
    }

    /**
     * 
     * @return array
     */
    public function getStashPrice() {
        if (!isset($this->stashprice)) {
            $this->stashprice = [
                'buy'  => $this->getCoins(),
                'sell' => $this->getCoins(),
            ];
            foreach ($this->getStash() as /* @var $item GuildStash */ $item) {
                $price                    = $item->getStashPrice();
                $this->stashprice['buy']  += $price['buy'];
                $this->stashprice['sell'] += $price['sell'];
            }
        }
        return $this->stashprice;
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        $name = (string) $this->getName();
        $tag  = (string) $this->getTag();
        if ($name && $tag) {
            return $name . ' [' . $tag . ']';
        }
        else {
            return $name;
        }
    }

}
