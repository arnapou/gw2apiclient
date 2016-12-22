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
     * @return string
     */
    public function getMOTD() {
        foreach ($this->getLog() as /* @var $log GuildLog */ $log) {
            if ($log->getType() == GuildLog::TYPE_MOTD) {
                return $log->getMotd();
            }
        }
        return '';
    }

    /**
     * 
     * @return integer
     */
    public function getMinLevel() {
        $min = 0;
        foreach ($this->getUpgrades() as /* @var $up GuildUpgrade */ $up) {
            $lvl = $up->getRequiredLevel();
            if ($lvl > $min) {
                $min = $lvl;
            }
        }
        return $min;
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
                    $obj                      = new GuildLog($env, $item);
                    $this->log[$obj->getId()] = $obj;
                }
            }
        }
        return $this->log;
    }

    /**
     * 
     * @return array
     */
    public function getTreasury() {
        if (empty($this->treasury) && $this->isLeader()) {
            $env  = $this->getEnvironment();
            $data = $env->getClientVersion2()->apiGuildTreasury($this->getId());
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $this->treasury[] = new GuildTreasury($env, $item);
                }
            }
        }
        return $this->treasury;
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
                    $obj                          = new GuildMember($env, $item);
                    $this->members[$obj->getId()] = $obj;
                }
                $ranks = $this->getRanks();
                uasort($this->members, function($a, $b) use($ranks) {
                    $ra = isset($ranks[$a->getRank()]) ? $ranks[$a->getRank()]->getOrder() : 999;
                    $rb = isset($ranks[$b->getRank()]) ? $ranks[$b->getRank()]->getOrder() : 999;
                    $sa = sprintf('%04d : %s', $ra, $a->getName());
                    $sb = sprintf('%04d : %s', $rb, $b->getName());
                    return strcasecmp($sa, $sb);
                });
            }
        }
        return $this->members;
    }

    /**
     * 
     * @return array
     */
    public function getUpgradeIds() {
        return $this->getEnvironment()->getClientVersion2()->apiGuildUpgrades($this->getId());
    }

    /**
     * 
     * @return array
     */
    public function getUpgrades() {
        if (empty($this->upgrades) && $this->isLeader()) {
            $env = $this->getEnvironment();
            $ids = $env->getClientVersion2()->apiGuildUpgrades($this->getId());
            if (!empty($ids) && is_array($ids)) {
                foreach ($ids as $id) {
                    $obj                           = new GuildUpgrade($env, $id);
                    $this->upgrades[$obj->getId()] = $obj;
                }
                uasort($this->upgrades, function($a, $b) {
                    return strcmp((string) $a, (string) $b);
                });
            }
        }
        return $this->upgrades;
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
