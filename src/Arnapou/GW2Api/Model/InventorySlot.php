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
class InventorySlot extends Item {

    /**
     *
     * @var string
     */
    protected $count;

    /**
     *
     * @var array
     */
    protected $infusions;

    /**
     *
     * @var array
     */
    protected $upgrades;

    /**
     *
     * @var array
     */
    protected $skin;

    /**
     *
     * @var array
     */
    protected $dataSlot;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client, $data['id']);

        $this->dataSlot = $data;
        $this->count    = isset($data['count']) ? $data['count'] : null;
    }

    /**
     * 
     * @return string
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * 
     * @return array
     */
    public function getInfusions() {
        if (!isset($this->infusions)) {
            $this->infusions = [];
            if (isset($this->dataSlot['infusions'])) {
                foreach ($this->dataSlot['infusions'] as $id) {
                    $this->infusions[] = new Item($this->client, $id);
                }
            }
        }
        return $this->infusions;
    }

    /**
     * 
     * @return array
     */
    public function getUpgrades() {
        if (!isset($this->upgrades)) {
            $this->upgrades = [];
            if (isset($this->dataSlot['upgrades'])) {
                foreach ($this->dataSlot['upgrades'] as $id) {
                    $this->upgrades[] = new Item($this->client, $id);
                }
            }
        }
        return $this->upgrades;
    }

    /**
     * 
     * @return Skin
     */
    public function getSkin() {
        if (!isset($this->skin)) {
            if (isset($this->dataSlot['skin'])) {
                $this->skin = new Skin($this->client, $this->dataSlot['skin']);
            }
        }
        return $this->skin;
    }

    public function getAgonyResistance() {
        if ($this->getRarity() !== self::RARITY_ASCENDED &&
            $this->getRarity() !== self::RARITY_LEGENDARY) {
            return null;
        }
        $sum = 0;
        foreach ($this->getInfusions()as /* @var $infusion Item */ $infusion) {
            $sum += $infusion->getAgonyResistance();
        }
        return $sum ? $sum : null;
    }

    public function getPrice() {
        $price               = parent::getPrice();
        $price['buy_total']  = $price['buy'] * $this->count;
        $price['sell_total'] = $price['sell'] * $this->count;
        return $price;
    }

}
