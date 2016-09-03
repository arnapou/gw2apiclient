<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Skills;

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Environment;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Model\Bag;
use Arnapou\GW2Api\Model\Build;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\Legend;
use Arnapou\GW2Api\Model\InventorySlot;
use Arnapou\GW2Api\Model\Specialization;
use Arnapou\GW2Api\Model\SpecializationLine;
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
     * @var integer
     */
    protected $cacheDuration = 300; // 5 minutes

    /**
     *
     * @var Environment 
     */
    protected $environment;

    public function __construct(Environment $env) {
        $this->environment = $env;
    }

    /**
     * 
     * @return Client
     */
    protected function getClient() {
        if (empty($this->client)) {
            $this->client = new Client($this->environment);
        }
        return $this->client;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getRace(Character $character) {
        $map = $this->getClient()->getMap('races');
        $key = strtolower($character->getRace());
        return isset($map[$key]) ? $map[$key] : '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getProfession(Character $character) {
        $map = $this->getClient()->getMap('professions');
        $key = strtolower($character->getData('profession'));
        return isset($map[$key]) ? $map[$key] : '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getWeapons(Character $character) {
        $map    = $this->getClient()->getMap('weapons');
        $return = [];

        $parts = [];
        foreach ([
        $character->getEquipmentWeapon1('A'),
        $character->getEquipmentWeapon2('A'),
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
        $character->getEquipmentWeapon1('B'),
        $character->getEquipmentWeapon2('B'),
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
        $map    = $this->getClient()->getMap('upgrades');
        $return = [];

        $parts = [];
        foreach ([
        $character->getEquipmentWeapon1('A'),
        $character->getEquipmentWeapon2('A'),
        $character->getEquipmentWeapon1('B'),
        $character->getEquipmentWeapon2('B'),
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
                $parts[] = '0';
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
                $parts[] = '0';
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
        if ($mode == 'pvp') {
            $mapStats   = $this->getClient()->getMap('items');
            $mapUpgrade = $this->getClient()->getMap('upgrades');
            $pvp        = $character->getPvpEquipment();
            if ($pvp && $pvp->getRune()) {
                $rune = $pvp->getRune();
                $part = $this->getMappedUpgrade($mode, $mapUpgrade, isset($rune) ? $rune : null)
                    . '.0.0';
            }
            else {
                $part = '0.0.0.0';
            }
            return implode(':', [$part, $part, $part, $part, $part, $part]);
        }
        else {
            $mapStats   = $this->getClient()->getMap('items');
            $mapUpgrade = $this->getClient()->getMap('upgrades');
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
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getUpgradesTrinkets(Character $character, $mode) {
        if ($mode == 'pvp') {
            $mapItems = $this->getClient()->getMap('pvp_items');
            $pvp      = $character->getPvpEquipment();
            $amulet   = $pvp ? $pvp->getAmulet() : null;
            $parts    = [];
            if ($amulet && isset($mapItems[$amulet->getId()])) {
                $parts[] = '0.0.0.' . $mapItems[$amulet->getId()];
            }
            else {
                $parts[] = '0.0.0.0';
            }
            $parts[] = '0.0.0.0';
            $parts[] = '0.0.0.0';
            $parts[] = '0.0.0.0';
            $parts[] = '0.0.0.0';
            $parts[] = '0.0.0.0';
            return implode(':', $parts);
        }
        else {
            $mapStats   = $this->getClient()->getMap('items');
            $mapUpgrade = $this->getClient()->getMap('upgrades');
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
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getUpgradesWeapons(Character $character, $mode) {
        if ($mode == 'pvp') {
            $mapUpgrade = $this->getClient()->getMap('upgrades');
            $pvp        = $character->getPvpEquipment();
            $sigils     = $pvp ? $pvp->getSigils() : [];
            $sigil1     = isset($sigils[0]) ? $sigils[0] : null;
            $sigil2     = isset($sigils[1]) ? $sigils[1] : null;
            $sigil3     = isset($sigils[2]) ? $sigils[2] : null;
            $sigil4     = isset($sigils[3]) ? $sigils[3] : null;
            $weapons    = [];
            $weaponA1   = $character->getEquipmentWeapon1('A');
            $weaponB1   = $character->getEquipmentWeapon1('B');
            if (\Arnapou\GW2Api\is_two_handed_weapon($weaponA1)) {
                $weapons[] = [$sigil1, $sigil2];
                $weapons[] = [null, null];
            }
            else {
                $weapons[] = [$sigil1, null];
                $weapons[] = [$sigil2, null];
            }
            if (\Arnapou\GW2Api\is_two_handed_weapon($weaponB1)) {
                $weapons[] = [$sigil3, $sigil4];
                $weapons[] = [null, null];
            }
            else {
                $weapons[] = [$sigil3, null];
                $weapons[] = [$sigil4, null];
            }
            $parts = [];
            foreach ($weapons as $weapon) {
                $parts[] = $this->getMappedUpgrade($mode, $mapUpgrade, $weapon[0])
                    . '.0.0'
                    . '.' . $this->getMappedUpgrade($mode, $mapUpgrade, $weapon[1]);
            }
            $parts[] = '0.0.0.0.0.0';
            $parts[] = '0.0.0.0.0.0';
            return implode(':', $parts);
        }
        else {
            $mapStats   = $this->getClient()->getMap('items');
            $mapUpgrade = $this->getClient()->getMap('upgrades');
            $parts      = [];
            foreach ([
            $character->getEquipmentWeapon1('A'),
            $character->getEquipmentWeapon2('A'),
            $character->getEquipmentWeapon1('B'),
            $character->getEquipmentWeapon2('B'),
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
    }

    /**
     * 
     * @param Character $character
     * @param string $mode
     * @return string
     */
    protected function getBuffs(Character $character, $mode) {
        $map = $this->getClient()->getMap('buffs');

        $parts     = ['0', '0'];
        $utilities = [];
        $foods     = [];
        foreach ($character->getBags() as /* @var $bag Bag */ $bag) {
            foreach ($bag->getInventorySlots() as /* @var $item Item */ $item) {
                if (empty($item)) {
                    continue;
                }
                if ($item->getType() === Item::TYPE_CONSUMABLE && $item->getLevel()) {
                    if ($item->getSubType() === Item::SUBTYPE_CONSUMABLE_FOOD) {
                        if (isset($map[$mode . '.' . $item->getId()])) {
                            $foods[floor($item->getLevel() / 10)][] = $item->getId();
                        }
                    }
                    elseif ($item->getSubType() === Item::SUBTYPE_CONSUMABLE_UTILITY) {
                        if (isset($map[$mode . '.' . $item->getId()])) {
                            $utilities[floor($item->getLevel() / 10)][] = $item->getId();
                        }
                    }
                }
            }
        }
        krsort($foods);
        krsort($utilities);

        foreach ([$foods, $utilities] as $i => $array) {
            foreach ($array as $level => $ids) {
                foreach ($ids as $id) {
                    $parts[$i] = $map[$mode . '.' . $id];
                    break;
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
            $rarity = $item->getRarity();
            $key    = $mode
                . '.' . strtolower($rarity == Item::RARITY_LEGENDARY ? Item::RARITY_ASCENDED : $rarity)
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
            $rarity = $item->getRarity();
            $key    = $mode
                . '.' . strtolower($rarity == Item::RARITY_LEGENDARY ? Item::RARITY_ASCENDED : $rarity)
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
            $rarity   = $item->getRarity();
            $statname = $item->getStatName();
            if (!empty($statname)) {
                $key = $mode
                    . '.' . strtolower($rarity == Item::RARITY_LEGENDARY ? Item::RARITY_ASCENDED : $rarity)
                    . '.' . strtolower($item->getType())
                    . '.' . strtolower($item->getSubType())
                    . '.' . strtolower($statname);
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
        $mapSpecializations = $this->getClient()->getMap('specializations');
        $mapTraits          = $this->getClient()->getMap('traits');
        $parts              = [];
        foreach ($build->getSpecializations() as /* @var $spe SpecializationLine */ $spe) {
            if (isset($mapSpecializations[$spe->getId()])) {
                $selected = $spe->getTraitsIds();
                $ids      = [$mapSpecializations[$spe->getId()], 'false', 'false', 'false'];
                foreach ($spe->getMajorTraits() as /* @var $trait SpecializationTrait */ $trait) {
                    if (in_array($trait->getId(), $selected) && isset($mapTraits[$trait->getId()])) {
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
     * @param Build $build
     * @return string
     */
    protected function getSkills(Build $build = null) {
        if ($build) {
            $mapSkills = $this->getClient()->getMap('skills');
            if ($build->getProfession() == Character::PROFESSION_REVENANT) {
                $parts = [[0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0]];
                foreach ($build->getLegends() as $k => $legend) {
                    if (isset($parts[$k])) {
                        foreach ([6, 7, 8, 9, 0] as $i => $skillIndex) {
                            $skill = $legend->getSkill($skillIndex);
                            if ($skill && $skill->getId()) {
                                if (isset($mapSkills[$skill->getId()])) {
                                    $parts[$k][$i] = $mapSkills[$skill->getId()];
                                }
                            }
                        }
                        $skill = $legend->getSkillSwap();
                        if ($skill && $skill->getId()) {
                            if (isset($mapSkills[$skill->getId()])) {
                                $parts[$k][5] = $mapSkills[$skill->getId()];
                            }
                        }
                    }
                }
                return implode('.', $parts[0]) . ':' . implode('.', $parts[1]);
            }
            else {
                $parts = [0, 0, 0, 0, 0];
                foreach ([6, 7, 8, 9, 0] as $i => $skillIndex) {
                    $skill = $build->getSkill($skillIndex);
                    if ($skill && $skill->getId()) {
                        if (isset($mapSkills[$skill->getId()])) {
                            $parts[$i] = $mapSkills[$skill->getId()];
                        }
                    }
                }
                return implode('.', $parts);
            }
        }
        else {
            return '0.0.0.0.0';
        }
    }

    /**
     * 
     * @param Build $build
     * @return string
     */
    protected function getAquaticSkills(Build $build = null) {
        if ($build) {
            // 
            // 
            // aquatic skills: not implemented for the moment because not in gw2 api
            // 
            //
            return '0.0.0.0.0';
        }
        else {
            return '0.0.0.0.0';
        }
    }

    /**
     * 
     * @param Build $build
     * @return string
     */
    protected function getPets(Build $build = null) {
        if ($build->getProfession() === Character::PROFESSION_RANGER) {
            $mapPets     = $this->getClient()->getMap('pets');
            $terrestrial = $build->getPetsTerrestrial();
            $aquatic     = $build->getPetsAquatic();
            $parts       = [0, 0, 0, 0];
            foreach ([0, 1] as $i) {
                if (isset($terrestrial[$i]) && isset($mapPets[$terrestrial[$i]->getId()])) {
                    $parts[$i] = $mapPets[$terrestrial[$i]->getId()];
                }
                if (isset($aquatic[$i]) && isset($mapPets[$aquatic[$i]->getId()])) {
                    $parts[$i + 2] = $mapPets[$aquatic[$i]->getId()];
                }
            }
            return implode('.', $parts);
        }
        else {
            return '0.0.0.0';
        }
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
            $env      = $character->getEnvironment();
            $client   = $env->getClientVersion2();
            $lang     = $env->getLang();
            $cache    = $env->getCache();
            $cacheKey = 'gw2skills-link/' . $lang . '/' . $mode . '/' . $character->getName();
            if ($cache) {
                $url = $cache->get($cacheKey);
                if ($url && !$nocache) {
                    return $url;
                }
            }
            if (!in_array($mode, ['pve', 'pvp', 'wvw'])) {
                throw new Exception('Mode not supported');
            }
            $build = $character->getBuild($mode);

            $data = [
                'mode' => $this->modes[$mode],
                'p'    => $this->getProfession($character),
                'r'    => $this->getRace($character),
                'w'    => $this->getWeapons($character),
                's'    => $this->getSkills($build),
                'sa'   => $this->getAquaticSkills($build),
                'pet'  => $this->getPets($build),
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
                        $cache->set($cacheKey, $url, $this->cacheDuration); // 15 min
                        return $url;
                    }
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

    /**
     * 
     * @return integer
     */
    function getCacheDuration() {
        return $this->cacheDuration;
    }

    /**
     * 
     * @param integer $duration
     * @return LinkBuilder
     */
    function setCacheDuration($duration) {
        $this->cacheDuration = $duration;
        return $this;
    }

}
