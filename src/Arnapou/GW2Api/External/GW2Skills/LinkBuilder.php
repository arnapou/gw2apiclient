<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\External\GW2Skills;

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Model\Bag;
use Arnapou\GW2Api\Model\Build;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\InventorySlot;
use Arnapou\GW2Api\Model\Specialization;
use Arnapou\GW2Api\Model\SpecializationTrait;

/* * ********************************************************************** *
 *                                                                          *
 *                               IMPORTANT                                  *
 *                                                                          *
 * ************************************************************************ *
 *                                                                          *
 *          In order to use this class without any problem, your IP         *
 *                  should be whitelisted by gw2skills.net                  *
 *                                                                          *
 *                      contact: info@gw2skills.net                         *
 *                                                                          *
 * ************************************************************************ */

class LinkBuilder {

    /**
     *
     * @var array
     */
    protected $modes = [
        'pvp' => 0,
        'pve' => 1,
        'wvw' => 2,
    ];

    /**
     *
     * @var Client
     */
    static protected $client;

    /**
     * 
     * @return Client
     */
    static protected function getClient() {
        if (empty(self::$client)) {
            self::$client = new Client();
        }
        return self::$client;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getRace(Character $character) {
        $map = self::getClient()->getMap('races');
        $key = strtolower($character->getRace());
        return isset($map[$key]) ? $map[$key] : '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getProfession(Character $character) {
        $map = self::getClient()->getMap('professions');
        $key = strtolower($character->getProfession());
        return isset($map[$key]) ? $map[$key] : '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getWeapons(Character $character) {
        $map    = self::getClient()->getMap('weapons');
        $return = [];

        $parts = [];
        foreach ([
        $character->getEquipment(Character::SLOT_WEAPON_A1),
        $character->getEquipment(Character::SLOT_WEAPON_A2),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0';
            }
            else {
                $key     = strtolower($equipment ? $equipment->getSubType() : '');
                $parts[] = isset($map[$key]) ? $map[$key] : '0';
            }
        }
        $return[] = implode('.', $parts);

        $parts = [];
        foreach ([
        $character->getEquipment(Character::SLOT_WEAPON_B1),
        $character->getEquipment(Character::SLOT_WEAPON_B2),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0';
            }
            else {
                $key     = strtolower($equipment ? $equipment->getSubType() : '');
                $parts[] = isset($map[$key]) ? $map[$key] : '0';
            }
        }
        $return[] = implode('.', $parts);

        return implode(':', $return);
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getInfusions(Character $character, $mode) {
        $map    = self::getClient()->getMap('upgrades');
        $return = [];

        $parts = [];
        foreach ([
        $character->getEquipment(Character::SLOT_WEAPON_A1),
        $character->getEquipment(Character::SLOT_WEAPON_A2),
        $character->getEquipment(Character::SLOT_WEAPON_B1),
        $character->getEquipment(Character::SLOT_WEAPON_B2),
        $character->getEquipment(Character::SLOT_WEAPON_AQUATIC_A),
        $character->getEquipment(Character::SLOT_WEAPON_AQUATIC_B),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0';
            }
            else {
                $infusions = $equipment->getInfusions();
                $parts[]   = $this->getMappedInfusion($mode, $map, isset($infusions[0]) ? $infusions[0] : null)
                    . '.' . $this->getMappedInfusion($mode, $map, isset($infusions[1]) ? $infusions[1] : null);
            }
        }
        $return[] = implode('.', $parts);

        $parts = [];
        foreach ([
        $character->getEquipment(Character::SLOT_HELM),
        $character->getEquipment(Character::SLOT_SHOULDERS),
        $character->getEquipment(Character::SLOT_COAT),
        $character->getEquipment(Character::SLOT_GLOVES),
        $character->getEquipment(Character::SLOT_LEGGINGS),
        $character->getEquipment(Character::SLOT_BOOTS),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0';
            }
            else {
                $infusions = $equipment->getInfusions();
                $parts[]   = $this->getMappedInfusion($mode, $map, isset($infusions[0]) ? $infusions[0] : null);
            }
        }
        $return[] = implode('.', $parts);

        $parts = [];
        foreach ([
        $character->getEquipment(Character::SLOT_AMULET),
        $character->getEquipment(Character::SLOT_RING1),
        $character->getEquipment(Character::SLOT_RING2),
        $character->getEquipment(Character::SLOT_ACCESSORY1),
        $character->getEquipment(Character::SLOT_ACCESSORY2),
        $character->getEquipment(Character::SLOT_BACKPACK),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0';
            }
            else {
                $infusions = $equipment->getInfusions();
                $parts[]   = $this->getMappedInfusion($mode, $map, isset($infusions[0]) ? $infusions[0] : null);
            }
        }
        $return[] = implode('.', $parts);

        return implode(':', $return);
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getUpgradesArmor(Character $character, $mode) {
        $mapStats   = self::getClient()->getMap('items');
        $mapUpgrade = self::getClient()->getMap('upgrades');
        $parts      = [];
        foreach ([
        $character->getEquipment(Character::SLOT_HELM),
        $character->getEquipment(Character::SLOT_SHOULDERS),
        $character->getEquipment(Character::SLOT_COAT),
        $character->getEquipment(Character::SLOT_GLOVES),
        $character->getEquipment(Character::SLOT_LEGGINGS),
        $character->getEquipment(Character::SLOT_BOOTS),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0.0.0';
            }
            else {
                $upgrades = $equipment->getUpgrades();
                $parts[]  = $this->getMappedUpgrade($mode, $mapUpgrade, isset($upgrades[0]) ? $upgrades[0] : null)
                    . '.' . $this->getMappedStat($mode, $mapStats, $equipment);
            }
        }
        return implode(':', $parts);
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getUpgradesTrinkets(Character $character, $mode) {
        $mapStats   = self::getClient()->getMap('items');
        $mapUpgrade = self::getClient()->getMap('upgrades');
        $parts      = [];
        foreach ([
        $character->getEquipment(Character::SLOT_AMULET),
        $character->getEquipment(Character::SLOT_RING1),
        $character->getEquipment(Character::SLOT_RING2),
        $character->getEquipment(Character::SLOT_ACCESSORY1),
        $character->getEquipment(Character::SLOT_ACCESSORY2),
        $character->getEquipment(Character::SLOT_BACKPACK),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0.0.0';
            }
            else {
                $upgrades = $equipment->getUpgrades();
                $parts[]  = $this->getMappedUpgrade($mode, $mapUpgrade, isset($upgrades[0]) ? $upgrades[0] : null)
                    . '.' . $this->getMappedStat($mode, $mapStats, $equipment);
            }
        }
        return implode(':', $parts);
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getUpgradesWeapons(Character $character, $mode) {
        $mapStats   = self::getClient()->getMap('items');
        $mapUpgrade = self::getClient()->getMap('upgrades');
        $parts      = [];
        foreach ([
        $character->getEquipment(Character::SLOT_WEAPON_A1),
        $character->getEquipment(Character::SLOT_WEAPON_A2),
        $character->getEquipment(Character::SLOT_WEAPON_B1),
        $character->getEquipment(Character::SLOT_WEAPON_B2),
        $character->getEquipment(Character::SLOT_WEAPON_AQUATIC_A),
        $character->getEquipment(Character::SLOT_WEAPON_AQUATIC_B),
        ] as $equipment) {
            if (empty($equipment)) {
                $parts[] = '0.0.0.0.0.0';
            }
            else {
                $upgrades = $equipment->getUpgrades();
                $parts[]  = $this->getMappedUpgrade($mode, $mapUpgrade, isset($upgrades[0]) ? $upgrades[0] : null)
                    . '.' . $this->getMappedStat($mode, $mapStats, $equipment)
                    . '.' . $this->getMappedUpgrade($mode, $mapUpgrade, isset($upgrades[1]) ? $upgrades[1] : null);
            }
        }
        return implode(':', $parts);
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getBuffs(Character $character, $mode) {
        $map = self::getClient()->getMap('buffs');

        $parts     = ['0', '0'];
        $utilities = [];
        $foods     = [];
        foreach ($character->getBags() as /* @var $bag Bag */ $bag) {
            foreach ($bag->getInventory() as /* @var $item Item */ $item) {
                if (empty($item)) {
                    continue;
                }
                if ($item->getType() === Item::TYPE_CONSUMABLE && $item->getLevel()) {
                    if ($item->getSubType() === Item::SUBTYPE_CONSUMABLE_FOOD) {
                        $foods[$item->getLevel()][] = $item->getId();
                    }
                    elseif ($item->getSubType() === Item::SUBTYPE_CONSUMABLE_UTILITY) {
                        $utilities[$item->getLevel()][] = $item->getId();
                    }
                }
            }
        }
        krsort($foods);
        krsort($utilities);

        foreach ([$foods, $utilities] as $i => $array) {
            foreach ($array as $level => $ids) {
                foreach ($ids as $id) {
                    $key = $mode . '.' . $id;
                    if (isset($map[$key])) {
                        $parts[$i] = $map[$key];
                        break;
                    }
                }
                if ($parts[$i] != 0) {
                    break;
                }
            }
        }

        return implode('.', $parts);
    }

    /**
     * 
     * @param string $mode
     * @param array $map
     * @param Item $item
     * @return string
     */
    protected function getMappedInfusion($mode, $map, Item $item = null) {
        if ($item) {
            $key = $mode
                . '.' . strtolower($item->getRarity())
                . '.' . $item->getId();
            if (isset($map[$key])) {
                return substr($map[$key], 2); // remove profile flag
            }
        }
        return '0';
    }

    /**
     * 
     * @param string $mode
     * @param array $map
     * @param Item $item
     * @return string
     */
    protected function getMappedUpgrade($mode, $map, Item $item = null) {
        if ($item) {
            $key = $mode
                . '.' . strtolower($item->getRarity())
                . '.' . $item->getId();
            if (isset($map[$key])) {
                return $map[$key];
            }
        }
        return '0.0';
    }

    /**
     * 
     * @param string $mode
     * @param array $map
     * @param InventorySlot $item
     * @return string
     */
    protected function getMappedStat($mode, $map, InventorySlot $item = null) {
        if ($item) {
            $attributes = $item->getAttributes();
            if (!empty($attributes)) {
                $key = $mode
                    . '.' . strtolower($item->getRarity())
                    . '.' . strtolower($item->getType())
                    . '.' . strtolower($item->getSubType())
                    . '.' . strtolower($attributes['stats_name']);
                if (isset($map[$key])) {
                    return $map[$key];
                }
            }
        }
        return '0.0';
    }

    /**
     * 
     * @param Build $build
     * @return string
     */
    protected function getTraits(Build $build = null) {
        if (empty($build)) {
            return '';
        }
        $mapSpecializations = self::getClient()->getMap('specializations');
        $mapTraits          = self::getClient()->getMap('traits');
        $parts              = [];
        foreach ($build->getSpecializations() as /* @var $spe Specialization */ $spe) {
            if (isset($mapSpecializations[$spe->getId()])) {
                $ids = [$mapSpecializations[$spe->getId()], 'false', 'false', 'false'];
                foreach ($spe->getMajorTraits() as /* @var $trait SpecializationTrait */ $trait) {
                    if ($trait->isSelected() && isset($mapTraits[$trait->getId()])) {
                        $ids[$trait->getTier()] = $mapTraits[$trait->getId()];
                    }
                }
                $parts[] = implode('.', $ids);
            }
        }
        return implode(':', $parts);
    }

    /**
     * 
     * @param Character $character
     * @param int $mode
     * @param boolean $nocache
     * @return string
     */
    public function getLink(Character $character, $mode, $nocache = false) {
        try {
            $client   = $character->getClient();
            $lang     = $client->getLang();
            $cache    = $client->getClientV2()->getRequestManager()->getCache();
            $cacheKey = 'gw2skills-link/' . $lang . '/' . $mode . '/' . $character->getName();
            if ($cache) {
                $url = $cache->get($cacheKey);
                if ($url && !$nocache) {
                    return $url;
                }
            }
            if ($mode === 'pve') {
                $build = $character->getBuildPve();
            }
            elseif ($mode === 'pvp') {
                $build = $character->getBuildPvp();
            }
            elseif ($mode === 'wvw') {
                $build = $character->getBuildWvw();
            }
            else {
                throw new Exception('Mode not supported');
            }

            $data = [
                'mode' => $this->modes[$mode],
                'p'    => $this->getProfession($character),
                'r'    => $this->getRace($character),
                'w'    => $this->getWeapons($character),
                's'    => '0.0.0.0.0', // skills: not implemented for the moment because not in gw2 api
                'sa'   => '0.0.0.0.0', // aquatic skills: not implemented for the moment because not in gw2 api
                'pet'  => '0.0.0.0', // pets: not implemented for the moment because not in gw2 api
                't'    => $this->getTraits($build),
                'up_w' => $this->getUpgradesWeapons($character, $mode),
                'up_b' => $this->getUpgradesArmor($character, $mode),
                'up_a' => $this->getUpgradesTrinkets($character, $mode),
                'inf'  => $this->getInfusions($character, $mode),
                'bf'   => $this->getBuffs($character, $mode),
            ];

            $curl     = new Curl();
            $curl->setUrl('http://api.gw2skills.net/v1/tcode/');
            $curl->setPost($data);
            $response = $curl->execute();

            if ($response->getErrorCode()) {
                return null;
            }

            if ($response->getInfoHttpCode() !== 200) {
                return null;
            }

            $content = $response->getContent();
            if ($content !== '' && $content[0] === '{') {
                $result = \Arnapou\GW2Api\json_decode($content);

                if (isset($result['quicklink'], $result['baseurl'])) {
                    $url = preg_replace('!^(https?://).*(gw2skills\.net)!si', '$1' . $lang . '.$2', $result['baseurl']) . $result['quicklink'];
                    if ($cache) {
                        $cache->set($cacheKey, $url, 900); // 15 min
                    }
                    return $url;
                }
            }
        }
        catch (Exception $e) {
            
        }
        return null;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    public function getLinkPvp(Character $character) {
        return $this->getLink($character, 'pvp');
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    public function getLinkWvw(Character $character) {
        return $this->getLink($character, 'wvw');
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    public function getLinkPve(Character $character) {
        return $this->getLink($character, 'pve');
    }

}
