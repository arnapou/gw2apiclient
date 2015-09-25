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
class Wardrobe extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $unlockedSkins;

    /**
     *
     * @var array
     */
    protected $allSkins;

    /**
     *
     * @var array
     */
    protected $backs;

    /**
     *
     * @var array
     */
    protected $armors;

    /**
     *
     * @var array
     */
    protected $weapons;

    /**
     *
     * @var boolean
     */
    static protected $cacheInitialized = false;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->unlockedSkins = $data;
        $this->allSkins      = $this->client->v2_skins();

        if (!self::$cacheInitialized) {
            $this->apiSkins($this->allSkins);
            self::$cacheInitialized = true;
        }
    }

    /**
     * 
     * @return integer
     */
    public function getCount() {
        return count($this->unlockedSkins);
    }

    /**
     * 
     */
    protected function prepareObjects() {
        $this->armors  = [
            Skin::WEIGHT_CLASS_CLOTHING => [],
            Skin::WEIGHT_CLASS_HEAVY    => [],
            Skin::WEIGHT_CLASS_MEDIUM   => [],
            Skin::WEIGHT_CLASS_LIGHT    => [],
        ];
        $this->weapons = [];

        $initData = [
            'skins' => [],
            'count' => 0,
            'total' => 0,
        ];

        $flippedUnlocked = array_flip($this->unlockedSkins);
        foreach ($this->allSkins as $id) {
            $unlocked = isset($flippedUnlocked[$id]);
            $skin     = new Skin($this->client, $id, $unlocked);

            if ($skin->hasFlag(Skin::FLAG_SHOW_IN_WARDROBE)) {
                $type    = $skin->getType();
                $subtype = $skin->getSubType();

                if ($type == Skin::TYPE_ARMOR) {
                    $weightClass = $skin->getArmorWeightClass();
                    if ($weightClass) {
                        if (!isset($this->armors[$weightClass][$subtype])) {
                            $this->armors[$weightClass][$subtype] = $initData;
                        }
                        $this->armors[$weightClass][$subtype]['skins'][$skin->getId()] = $skin;
                        $this->armors[$weightClass][$subtype]['count'] += $unlocked ? 1 : 0;
                        $this->armors[$weightClass][$subtype]['total'] ++;
                    }
                }
                elseif ($type == Skin::TYPE_BACK) {
                    if (!isset($this->backs[$type])) {
                        $this->backs[$type] = $initData;
                    }
                    $this->backs[$type]['skins'][$skin->getId()] = $skin;
                    $this->backs[$type]['count'] += $unlocked ? 1 : 0;
                    $this->backs[$type]['total'] ++;
                }
                elseif ($type == Skin::TYPE_WEAPON) {
                    if (!isset($this->weapons[$subtype])) {
                        $this->weapons[$subtype] = $initData;
                    }

                    $this->weapons[$subtype]['skins'][$skin->getId()] = $skin;
                    $this->weapons[$subtype]['count'] += $unlocked ? 1 : 0;
                    $this->weapons[$subtype]['total'] ++;
                }
            }
        }
        ksort($this->weapons);
        ksort($this->armors[Skin::WEIGHT_CLASS_CLOTHING]);
        ksort($this->armors[Skin::WEIGHT_CLASS_HEAVY]);
        ksort($this->armors[Skin::WEIGHT_CLASS_MEDIUM]);
        ksort($this->armors[Skin::WEIGHT_CLASS_LIGHT]);
    }

    /**
     * 
     * @return array
     */
    public function getArmorsLight() {
        if (!isset($this->armors)) {
            $this->prepareObjects();
        }
        return $this->armors[Skin::WEIGHT_CLASS_LIGHT];
    }

    /**
     * 
     * @return array
     */
    public function getArmorsMedium() {
        if (!isset($this->armors)) {
            $this->prepareObjects();
        }
        return $this->armors[Skin::WEIGHT_CLASS_MEDIUM];
    }

    /**
     * 
     * @return array
     */
    public function getArmorsHeavy() {
        if (!isset($this->armors)) {
            $this->prepareObjects();
        }
        return $this->armors[Skin::WEIGHT_CLASS_HEAVY];
    }

    /**
     * 
     * @return array
     */
    public function getArmorsClothing() {
        if (!isset($this->armors)) {
            $this->prepareObjects();
        }
        return $this->armors[Skin::WEIGHT_CLASS_CLOTHING];
    }

    /**
     * 
     * @return array
     */
    public function getBacks() {
        if (!isset($this->backs)) {
            $this->prepareObjects();
        }
        return $this->backs;
    }

    /**
     * 
     * @return array
     */
    public function getWeapons() {
        if (!isset($this->weapons)) {
            $this->prepareObjects();
        }
        return $this->weapons;
    }

}
