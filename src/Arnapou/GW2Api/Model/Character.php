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
use Arnapou\GW2Api\External\GW2SkillsLinkBuilder;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Character extends AbstractObject {

    // RACES
    const RACE_ASURA              = 'Asura';
    const RACE_CHARR              = 'Charr';
    const RACE_HUMAN              = 'Human';
    const RACE_NORN               = 'Norn';
    const RACE_SYLVARI            = 'Sylvari';
    // GENDERS
    const GENDER_MALE             = 'Male';
    const GENDER_FEMALE           = 'Female';
    // PROFESSIONS
    const PROFESSION_ELEMENTALIST = 'Elementalist';
    const PROFESSION_ENGINEER     = 'Engineer';
    const PROFESSION_GUARDIAN     = 'Guardian';
    const PROFESSION_MESMER       = 'Mesmer';
    const PROFESSION_NECROMANCER  = 'Necromancer';
    const PROFESSION_RANGER       = 'Ranger';
    const PROFESSION_THIEF        = 'Thief';
    const PROFESSION_WARRIOR      = 'Warrior';
    const PROFESSION_REVENANT     = 'Revenant';
    // SLOTS
    const SLOT_HELM_AQUATIC       = 'HelmAquatic';
    const SLOT_HELM               = 'Helm';
    const SLOT_SHOULDERS          = 'Shoulders';
    const SLOT_COAT               = 'Coat';
    const SLOT_GLOVES             = 'Gloves';
    const SLOT_LEGGINGS           = 'Leggings';
    const SLOT_BOOTS              = 'Boots';
    const SLOT_BACKPACK           = 'Backpack';
    const SLOT_AMULET             = 'Amulet';
    const SLOT_ACCESSORY1         = 'Accessory1';
    const SLOT_ACCESSORY2         = 'Accessory2';
    const SLOT_RING1              = 'Ring1';
    const SLOT_RING2              = 'Ring2';
    const SLOT_WEAPON_AQUATIC_A   = 'WeaponAquaticA';
    const SLOT_WEAPON_AQUATIC_B   = 'WeaponAquaticB';
    const SLOT_WEAPON_A1          = 'WeaponA1';
    const SLOT_WEAPON_A2          = 'WeaponA2';
    const SLOT_WEAPON_B1          = 'WeaponB1';
    const SLOT_WEAPON_B2          = 'WeaponB2';
    const SLOT_SICKLE             = 'Sickle';
    const SLOT_AXE                = 'Axe';
    const SLOT_PICK               = 'Pick';

    /**
     *
     * @var array
     */
    protected $craftingDisciplines;

    /**
     *
     * @var Guild
     */
    protected $guild;

    /**
     *
     * @var array
     */
    protected $equipments;

    /**
     *
     * @var array
     */
    protected $bags;

    /**
     *
     * @var array
     */
    protected $attributes;

    /**
     *
     * @var array
     */
    protected $bagsfilling;

    /**
     *
     * @var array
     */
    protected $bagsprice;

    /**
     *
     * @var array
     */
    protected $builds;

    /**
     *
     * @var array
     */
    protected $inventoryStuff;

    /**
     * 
     * @param SimpleClient $client
     * @param integer $name
     */
    public function __construct(SimpleClient $client, $name) {
        parent::__construct($client);

        $data = $this->apiCharacters($name);
        if (!is_array($data) || !isset($data['name'])) {
            throw new Exception('Invalid received character data.');
        }
        $this->data = $data;
    }

    /**
     * 
     * @param string $type
     * @return Build
     */
    protected function getBuild($type) {
        if (!isset($this->builds[$type])) {
            $data = $this->getSubkey(['specializations', $type]);
            if ($data) {
                $this->builds[$type] = new Build($this->client, $data);
            }
        }
        return $this->builds[$type];
    }

    /**
     * 
     * @return Build
     */
    public function getBuildPvp() {
        return $this->getBuild('pvp');
    }

    /**
     * 
     * @return Build
     */
    public function getBuildPve() {
        return $this->getBuild('pve');
    }

    /**
     * 
     * @return Build
     */
    public function getBuildWvw() {
        return $this->getBuild('wvw');
    }

    /**
     * 
     * @return array
     */
    public function getInventoryStuff() {
        if (!isset($this->inventoryStuff)) {

            $this->inventoryStuff = [];

            foreach ($this->getEquipments() as /* @var $item InventorySlot */ $item) {
                $this->inventoryStuff[$item->getSubType()][] = $item;
            }

            foreach ($this->getBags() as /* @var $bag Bag */ $bag) {
                foreach ($bag->getInventoryStuff() as $subtype => $items) {
                    foreach ($items as /* @var $item InventorySlot */ $item) {
                        $this->inventoryStuff[$subtype][] = $item;
                    }
                }
            }
        }
        return $this->inventoryStuff;
    }

    /**
     * @return array
     */
    public function getAttributes() {
        if (!isset($this->attributes)) {
            $attributes = [
                'Power'     => [ 'WeaponA' => 1000, 'WeaponB' => 1000, 'WeaponAquatic' => 1000],
                'Precision' => [ 'WeaponA' => 1000, 'WeaponB' => 1000, 'WeaponAquatic' => 1000],
                'Toughness' => [ 'WeaponA' => 1000, 'WeaponB' => 1000, 'WeaponAquatic' => 1000],
                'Vitality'  => [ 'WeaponA' => 1000, 'WeaponB' => 1000, 'WeaponAquatic' => 1000],
                'Ferocity'  => [ 'WeaponA' => 0, 'WeaponB' => 0, 'WeaponAquatic' => 0],
                'Condition' => [ 'WeaponA' => 0, 'WeaponB' => 0, 'WeaponAquatic' => 0],
                'Healing'   => [ 'WeaponA' => 0, 'WeaponB' => 0, 'WeaponAquatic' => 0],
                'AR'        => [ 'WeaponA' => 0, 'WeaponB' => 0, 'WeaponAquatic' => 0],
            ];
            $unknown    = [];
            foreach ($this->getEquipments() as $slot => /* @var $equipment Equipment */ $equipment) {
                if (in_array($slot, [self::SLOT_AXE, self::SLOT_SICKLE, self::SLOT_PICK])) {
                    continue;
                }
                $attrs = $equipment->getAttributes();
                if (empty($attrs)) {
                    $unknown[] = $slot;
                }
                if (strpos($slot, 'WeaponAquatic') === 0) {
                    $attributes['AR']['WeaponA'] += $equipment->getAgonyResistance();
                    if (!empty($attrs)) {
                        foreach ($attrs['list'] as $attr => $value) {
                            $attributes[$attr]['WeaponA'] += $value;
                        }
                    }
                }
                elseif (strpos($slot, 'WeaponA') === 0) {
                    $attributes['AR']['WeaponB'] += $equipment->getAgonyResistance();
                    if (!empty($attrs)) {
                        foreach ($attrs['list'] as $attr => $value) {
                            $attributes[$attr]['WeaponB'] += $value;
                        }
                    }
                }
                elseif (strpos($slot, 'WeaponB') === 0) {
                    $attributes['AR']['WeaponAquatic'] += $equipment->getAgonyResistance();
                    if (!empty($attrs)) {
                        foreach ($attrs['list'] as $attr => $value) {
                            $attributes[$attr]['WeaponAquatic'] += $value;
                        }
                    }
                }
                else {
                    $attributes['AR']['WeaponA'] += $equipment->getAgonyResistance();
                    $attributes['AR']['WeaponB'] += $equipment->getAgonyResistance();
                    $attributes['AR']['WeaponAquatic'] += $equipment->getAgonyResistance();
                    if (!empty($attrs)) {
                        foreach ($attrs['list'] as $attr => $value) {
                            $attributes[$attr]['WeaponA'] += $value;
                            $attributes[$attr]['WeaponB'] += $value;
                            $attributes[$attr]['WeaponAquatic'] += $value;
                        }
                    }
                }
            }
            foreach ($attributes as $key => &$values) {
                if ($values['WeaponA'] == $values['WeaponB'] && $values['WeaponA'] == $values['WeaponAquatic']) {
                    $values = ['All' => $values['WeaponA']];
                }
            }
            foreach ($attributes['Precision'] as $set => $value) {
                $attributes['PrecisionPct'][$set] = round(($value - 916) / 21);
            }
            foreach ($attributes['Ferocity'] as $set => $value) {
                $attributes['FerocityPct'][$set] = round(150 + $value / 15);
            }
            $this->attributes = [
                'unknown' => $unknown,
                'list'    => $attributes,
            ];
        }
        return $this->attributes;
    }

    /**
     * @param string $slot
     * @return Equipment
     */
    public function getEquipment($slot) {
        $equipments = $this->getEquipments();
        if (isset($equipments[$slot])) {
            return $equipments[$slot];
        }
        return null;
    }

    /**
     * 
     * @return array
     */
    public function getEquipments() {
        if (!isset($this->equipments)) {
            $this->equipments = [];
            if (!empty($this->data['equipment']) && is_array($this->data['equipment'])) {

                $this->preloadSlots($this->data['equipment']);

                foreach ($this->data['equipment'] as $equipment) {
                    if (!isset($equipment['id'], $equipment['slot'])) {
                        continue;
                    }
                    $this->equipments[$equipment['slot']] = new Equipment($this->client, $equipment);
                }
            }
        }
        return $this->equipments;
    }

    /**
     * 
     * @return array
     */
    public function getBags() {
        if (!isset($this->bags)) {
            $this->bags = [];
            if (!empty($this->data['bags']) && is_array($this->data['bags'])) {

                foreach ($this->data['bags'] as $bag) {
                    if (isset($bag['inventory'])) {
                        $this->preloadSlots($bag['inventory']);
                    }
                }

                foreach ($this->data['bags'] as $bag) {
                    if (isset($bag['inventory'])) {
                        $this->bags[] = new Bag($this->client, $bag);
                    }
                }
            }
        }
        return $this->bags;
    }

    /**
     * 
     * @return string
     */
    public function getGw2SkillsLink() {
        $builder = new GW2SkillsLinkBuilder();
        return $builder->getLink($this);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->data['name'];
    }

    /**
     * 
     * @return integer
     */
    public function getRace() {
        return $this->data['race'];
    }

    /**
     * 
     * @return string
     */
    public function getGender() {
        return $this->data['gender'];
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

    /**
     * 
     * @return string
     */
    public function getLevel() {
        return $this->data['level'];
    }

    /**
     * 
     * @return Guild
     */
    public function getGuild() {
        if (!isset($this->guild)) {
            if (empty($this->data['guild'])) {
                return null;
            }
            $this->guild = new Guild($this->client, $this->data['guild']);
        }
        return $this->guild;
    }

    /**
     * 
     * @param boolean $onlyactive
     * @return array
     */
    public function getCrafting($onlyactive = true) {
        if (!isset($this->craftingDisciplines)) {
            $this->craftingDisciplines = [];
            if (!empty($this->data['crafting']) && is_array($this->data['crafting'])) {
                foreach ($this->data['crafting'] as $discipline) {
                    $this->craftingDisciplines[] = new CraftingDiscipline($this->client, $discipline);
                }
            }
        }
        if ($onlyactive) {
            $disciplines = [];
            foreach ($this->craftingDisciplines as /* @var $discipline CraftingDiscipline */ $discipline) {
                if ($discipline->isActive()) {
                    $disciplines[] = $discipline;
                }
            }
            return $disciplines;
        }
        return $this->craftingDisciplines;
    }

    /**
     * 
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getCreated() {
        if (isset($this->data['created'])) {
            return gmdate('Y-m-d H:i', strtotime($this->data['created']));
        }
        return null;
    }

    /**
     * 
     * @return int
     */
    public function getDays() {
        if (isset($this->data['created'])) {
            return floor((time() - strtotime($this->data['created'])) / 86400);
        }
        return null;
    }

    /**
     * 
     * @return int
     */
    public function getAge() {
        return $this->getSubkey(['age']);
    }

    /**
     * 
     * @return int
     */
    public function getDeaths() {
        return $this->getSubkey(['deaths']);
    }

    /**
     * 
     * @return array
     */
    public function getBagsPrice() {
        if (!isset($this->bagsprice)) {
            $this->bagsprice = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->getBags() as /* @var $bag Bag */ $bag) {
                if ($bag) {
                    $price = $bag->getBagPrice();
                    $this->bagsprice['buy'] += $price['buy'];
                    $this->bagsprice['sell'] += $price['sell'];
                }
            }
        }
        return $this->bagsprice;
    }

    /**
     * 
     * @return array
     */
    public function getBagsFilling() {
        if (!isset($this->bagsfilling)) {
            $this->bagsfilling = [
                'filled' => 0,
                'size'   => 0,
            ];
            foreach ($this->getBags() as /* @var $bag Bag */ $bag) {
                if ($bag) {
                    $this->bagsfilling['size'] += $bag->getSize();
                    foreach ($bag->getInventory() as /* @var $item InventorySlot */ $item) {
                        if ($item) {
                            $this->bagsfilling['filled'] ++;
                        }
                    }
                }
            }
        }
        return $this->bagsfilling;
    }

}
