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

use Arnapou\GW2Skills\LinkBuilder;

/**
 * @doc https://wiki.guildwars2.com/wiki/API:2/characters
 *
 * @method string  getAge()
 * @method string  getDeaths()
 * @method string  getGender()
 * @method string  getLevel()
 * @method string  getName()
 * @method string  getRace()
 */
class Character extends AbstractObject
{

    // RACES
    const RACE_ASURA   = 'Asura';
    const RACE_CHARR   = 'Charr';
    const RACE_HUMAN   = 'Human';
    const RACE_NORN    = 'Norn';
    const RACE_SYLVARI = 'Sylvari';
    // GENDERS
    const GENDER_MALE   = 'Male';
    const GENDER_FEMALE = 'Female';
    // PROFESSIONS
    const PROFESSION_ELEMENTALIST = 'Elementalist';
    const PROFESSION_TEMPEST      = 'Tempest';
    const PROFESSION_WEAVER       = 'Weaver';
    const PROFESSION_ENGINEER     = 'Engineer';
    const PROFESSION_SCRAPPER     = 'Scrapper';
    const PROFESSION_HOLOSMITH    = 'Holosmith';
    const PROFESSION_GUARDIAN     = 'Guardian';
    const PROFESSION_DRAGONHUNTER = 'DragonHunter';
    const PROFESSION_FIREBRAND    = 'Firebrand';
    const PROFESSION_MESMER       = 'Mesmer';
    const PROFESSION_CHRONOMANCER = 'Chronomancer';
    const PROFESSION_MIRAGE       = 'Mirage';
    const PROFESSION_NECROMANCER  = 'Necromancer';
    const PROFESSION_REAPER       = 'Reaper';
    const PROFESSION_SCOURGE      = 'Scourge';
    const PROFESSION_RANGER       = 'Ranger';
    const PROFESSION_DRUID        = 'Druid';
    const PROFESSION_SOULBEAST    = 'Soulbeast';
    const PROFESSION_THIEF        = 'Thief';
    const PROFESSION_DAREDEVIL    = 'Daredevil';
    const PROFESSION_DEADEYE      = 'Deadeye';
    const PROFESSION_WARRIOR      = 'Warrior';
    const PROFESSION_BERSERKER    = 'Berserker';
    const PROFESSION_SPELLBREAKER = 'Spellbreaker';
    const PROFESSION_REVENANT     = 'Revenant';
    const PROFESSION_HERALD       = 'Herald';
    const PROFESSION_RENEGADE     = 'Renegade';
    // SLOTS
    const SLOT_HELM_AQUATIC     = 'HelmAquatic';
    const SLOT_HELM             = 'Helm';
    const SLOT_SHOULDERS        = 'Shoulders';
    const SLOT_COAT             = 'Coat';
    const SLOT_GLOVES           = 'Gloves';
    const SLOT_LEGGINGS         = 'Leggings';
    const SLOT_BOOTS            = 'Boots';
    const SLOT_BACKPACK         = 'Backpack';
    const SLOT_AMULET           = 'Amulet';
    const SLOT_ACCESSORY1       = 'Accessory1';
    const SLOT_ACCESSORY2       = 'Accessory2';
    const SLOT_RING1            = 'Ring1';
    const SLOT_RING2            = 'Ring2';
    const SLOT_WEAPON_AQUATIC_A = 'WeaponAquaticA';
    const SLOT_WEAPON_AQUATIC_B = 'WeaponAquaticB';
    const SLOT_WEAPON_A1        = 'WeaponA1';
    const SLOT_WEAPON_A2        = 'WeaponA2';
    const SLOT_WEAPON_B1        = 'WeaponB1';
    const SLOT_WEAPON_B2        = 'WeaponB2';
    const SLOT_SICKLE           = 'Sickle';
    const SLOT_AXE              = 'Axe';
    const SLOT_PICK             = 'Pick';

    /**
     *
     * @var array
     */
    protected $crafting = [];

    /**
     *
     * @var array
     */
    protected $backstory = [];

    /**
     *
     * @var array
     */
    protected $training;

    /**
     *
     * @var boolean
     */
    protected $backstorySorted = false;

    /**
     *
     * @var array
     */
    protected $builds = [];

    /**
     *
     * @var array
     */
    protected $bags = [];

    /**
     *
     * @var array
     */
    protected $wvwAbilities = [];

    /**
     *
     * @var array
     */
    protected $equipments = [];

    /**
     *
     * @var PvpEquipment
     */
    protected $pvpEquipment;

    /**
     *
     * @var Guild
     */
    protected $guild = null;

    /**
     *
     * @var string
     */
    protected $profession = null;

    /**
     *
     * @var Profession
     */
    protected $professionObject = null;

    /**
     *
     * @var array
     */
    protected $attributes = null;

    /**
     *
     * @var Title
     */
    protected $title = null;

    /**
     *
     * @var array
     */
    protected $bagsprice;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['title'])) {
            $this->title = new Title($this->getEnvironment(), $data['title']);
        }
        if (isset($data['backstory']) && is_array($data['backstory'])) {
            foreach ($data['backstory'] as $id) {
                $this->backstory[] = new BackstoryAnswer($this->getEnvironment(), $id);
            }
        }
        if (isset($data['crafting']) && is_array($data['crafting'])) {
            foreach ($data['crafting'] as $item) {
                if (isset($item['discipline'])) {
                    $this->crafting[] = new Crafting($this->getEnvironment(), $item);
                }
            }
        }
        if (isset($data['bags']) && is_array($data['bags'])) {
            foreach ($data['bags'] as $item) {
                $this->bags[] = new Bag($this->getEnvironment(), $item);
            }
        }
        if (isset($data['equipment']) && is_array($data['equipment'])) {
            foreach ($data['equipment'] as $item) {
                if (isset($item['slot'])) {
                    $this->equipments[$item['slot']] = new Equipment($this->getEnvironment(), $item);
                }
            }
        }
        if (!empty($data['wvw_abilities']) && is_array($data['wvw_abilities'])) {
            foreach ($data['wvw_abilities'] as $item) {
                if (isset($item['id'])) {
                    $wvwAbility = new WvwAbility($this->getEnvironment(), $item['id']);
                    if (isset($item['rank'])) {
                        $wvwAbility->setRank($item['rank']);
                    }
                    $this->wvwAbilities[] = $wvwAbility;
                }
            }
        }
        if (isset($data['equipment_pvp'])) {
            $this->pvpEquipment = new PvpEquipment($this->getEnvironment(), $data['equipment_pvp']);
        }
        foreach ([Build::TYPE_PVE, Build::TYPE_PVP, Build::TYPE_WVW] as $type) {
            $this->builds[$type] = new Build($this->getEnvironment(), [
                'type'            => $type,
                'specializations' => $this->getData(['specializations', $type], []),
                'skills'          => $this->getData(['skills', $type], []),
            ]);
        }
    }

    /**
     *
     * @return array
     */
    public function getWvwAbilities()
    {
        return $this->wvwAbilities;
    }

    /**
     *
     * @return PvpEquipment
     */
    public function getPvpEquipment()
    {
        return $this->pvpEquipment;
    }

    /**
     *
     * @return string
     */
    public function getGw2SkillsLink($mode)
    {
        if (!in_array($mode, ['pve', 'pvp', 'wvw'])) {
            throw new \Exception('Mode should be either "pve", "pvp" or "wvw"');
        }
        $builder = new LinkBuilder($this->getEnvironment());
        return $builder->getLink($this, $mode);
    }

    /**
     *
     * @return array
     */
    public function getBagsPrice()
    {
        if (!isset($this->bagsprice)) {
            $this->bagsprice = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->getBags() as $bag) {
                if ($bag) {
                    $price                   = $bag->getBagPrice();
                    $this->bagsprice['buy']  += $price['buy'];
                    $this->bagsprice['sell'] += $price['sell'];
                }
            }
        }
        return $this->bagsprice;
    }

    /**
     *
     * @return Build
     */
    public function getBuild($type)
    {
        $builds = $this->getBuilds();
        return isset($builds[$type]) ? $builds[$type] : null;
    }

    /**
     *
     * @return Build[]
     */
    public function getBuilds()
    {
        return $this->builds;
    }

    /**
     *
     * @return Bag[]
     */
    public function getBags()
    {
        return $this->bags;
    }

    /**
     *
     * @return InventorySlot[]
     */
    public function getEquipments()
    {
        return $this->equipments;
    }

    /**
     *
     * @return Equipment
     */
    public function getEquipmentsBySubtype()
    {
        $data = [];
        foreach ($this->getEquipments() as $slot => $item) {
            if (in_array($slot, [self::SLOT_AXE, self::SLOT_PICK, self::SLOT_SICKLE])) {
                $key = $item->getSubType();
            } else {
                $key = $item->getType() == Item::TYPE_BACK ? self::SLOT_BACKPACK : $item->getSubType();
            }
            $data[$key][] = $item;
        }
        $allowedRarities = [Item::RARITY_LEGENDARY, Item::RARITY_ASCENDED, Item::RARITY_EXOTIC];
        $allowedTypes    = [Item::TYPE_ARMOR, Item::TYPE_BACK, Item::TYPE_WEAPON, Item::TYPE_TRINKET, Item::TYPE_GATHERING];
        foreach ($this->getBags() as $bag) {
            foreach ($bag->getInventorySlots() as $item) {
                if (empty($item) ||
                    !in_array($item->getRarity(), $allowedRarities) ||
                    !in_array($item->getType(), $allowedTypes)
                ) {
                    continue;
                }
                $key          = $item->getType() == Item::TYPE_BACK ? self::SLOT_BACKPACK : $item->getSubType();
                $data[$key][] = $item;
            }
        }

        return $data;
    }

    /**
     *
     * @return Equipment
     */
    public function getEquipment($slot)
    {
        return isset($this->equipments[$slot]) ? $this->equipments[$slot] : null;
    }

    /**
     *
     * @return boolean
     */
    public function canSwapWeapons()
    {
        $profession = $this->getData('profession');
        if ($profession === self::PROFESSION_ELEMENTALIST || $profession === self::PROFESSION_ENGINEER) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param bool $object
     * @return array
     */
    public function getProfession($object = false)
    {
        if ($object) {
            if ($this->professionObject === null) {
                $this->professionObject = new Profession($this->getEnvironment(), $this->getData('profession'));
            }
            return $this->professionObject;
        } else {
            if ($this->profession === null) {
                $this->profession = $this->getData('profession');
                $build            = $this->getBuild('pve');
                /* @var $build Build */
                if ($build && $this->profession) {
                    $mapping = [
                        48 => self::PROFESSION_TEMPEST, 56 => self::PROFESSION_WEAVER,
                        43 => self::PROFESSION_SCRAPPER, 57 => self::PROFESSION_HOLOSMITH,
                        27 => self::PROFESSION_DRAGONHUNTER, 62 => self::PROFESSION_FIREBRAND,
                        40 => self::PROFESSION_CHRONOMANCER, 59 => self::PROFESSION_MIRAGE,
                        34 => self::PROFESSION_REAPER, 60 => self::PROFESSION_SCOURGE,
                        5  => self::PROFESSION_DRUID, 55 => self::PROFESSION_SOULBEAST,
                        52 => self::PROFESSION_HERALD, 63 => self::PROFESSION_RENEGADE,
                        7  => self::PROFESSION_DAREDEVIL, 58 => self::PROFESSION_DEADEYE,
                        18 => self::PROFESSION_BERSERKER, 61 => self::PROFESSION_SPELLBREAKER,
                    ];
                    foreach ($build->getSpecializations() as $specialization) {
                        if ($specialization->isElite() && isset($mapping[$specialization->getId()])) {
                            $this->profession = $mapping[$specialization->getId()];
                            break;
                        }
                    }
                }
            }
            return $this->profession;
        }
    }

    /**
     *
     * @return array
     */
    public function getCrafting()
    {
        return $this->crafting;
    }

    /**
     *
     * @return array
     */
    public function getBackstoryAnswers()
    {
        if (!$this->backstorySorted) {
            usort($this->backstory, function ($a, $b) {
                try {
                    $oa = $a->getQuestion()->getOrder();
                    $ob = $b->getQuestion()->getOrder();
                    if ($oa == $ob) {
                        return 0;
                    }
                    return $oa > $ob ? 1 : -1;
                } catch (\Exception $ex) {
                    return 0;
                }
            });
            $this->backstorySorted = true;
        }
        return $this->backstory;
    }

    /**
     *
     * @return array
     */
    public function getTraining()
    {
        if (!isset($this->training)) {
            $this->training = [];
            $charTraining   = $this->getData('training');
            if (is_array($charTraining)) {
                $profession = $this->getProfession(true);
                if ($profession) {
                    $profTraining = $profession->getTraining();
                    $env          = $this->getEnvironment();
                    foreach ($charTraining as $item) {
                        if (isset($item['id'], $profTraining[$item['id']])) {
                            $item['training']            = $profTraining[$item['id']];
                            $this->training[$item['id']] = $item;
                        }
                    }
                }
            }
        }
        return $this->training;
    }

    /**
     *
     * @return Guild
     */
    public function getGuild()
    {
        $guildId = $this->getGuildId();
        if ($guildId && empty($this->guild)) {
            try {
                $data = $this->getEnvironment()->getClientVersion2()->apiGuild($guildId);
                if (isset($data['id'])) {
                    $this->guild = new Guild($this->getEnvironment(), $data);
                }
            } catch (\Exception $e) {

            }
        }
        return $this->guild;
    }

    /**
     *
     * @return integer
     */
    public function getGuildId()
    {
        return $this->getData('guild');
    }

    /**
     *
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getCreated()
    {
        $date = $this->getData('created');
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    /**
     *
     * @return integer
     */
    public function getTitleId()
    {
        return (int)$this->getData('title');
    }

    /**
     *
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return int
     */
    public function getCreatedEllapsedTime()
    {
        $created = $this->getCreated();
        if ($created) {
            return time() - strtotime($created);
        }
        return null;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     *
     * @return InventorySlot
     */
    public function getEquipmentWeapon1($set = 'A')
    {
        if (!in_array($set, ['A', 'B'])) {
            throw new Exception('Set should be either "A" or "B"');
        }
        if ($set == 'A') {
            $weaponA1 = $this->getEquipment(self::SLOT_WEAPON_A1);
            $weaponA2 = $this->getEquipment(self::SLOT_WEAPON_A2);
            $weaponB1 = $this->getEquipment(self::SLOT_WEAPON_B1);
        } else {
            $weaponA1 = $this->getEquipment(self::SLOT_WEAPON_B1);
            $weaponA2 = $this->getEquipment(self::SLOT_WEAPON_B2);
            $weaponB1 = $this->getEquipment(self::SLOT_WEAPON_A1);
        }
        if ($weaponA1) {
            return $weaponA1;
        } elseif ($weaponA2) {
            if ($weaponB1 && !\Arnapou\GW2Api\is_two_handed_weapon($weaponB1)) {
                return $weaponB1;
            }
        } else {
            return $weaponB1;
        }
        return null;
    }

    /**
     *
     * @return InventorySlot
     */
    public function getEquipmentWeapon2($set = 'A')
    {
        if (!in_array($set, ['A', 'B'])) {
            throw new Exception('Set should be either "A" or "B"');
        }
        if ($set == 'A') {
            $weaponA1 = $this->getEquipment(self::SLOT_WEAPON_A1);
            $weaponA2 = $this->getEquipment(self::SLOT_WEAPON_A2);
            $weaponB1 = $this->getEquipment(self::SLOT_WEAPON_B1);
            $weaponB2 = $this->getEquipment(self::SLOT_WEAPON_B2);
        } else {
            $weaponA1 = $this->getEquipment(self::SLOT_WEAPON_B1);
            $weaponA2 = $this->getEquipment(self::SLOT_WEAPON_B2);
            $weaponB1 = $this->getEquipment(self::SLOT_WEAPON_A1);
            $weaponB2 = $this->getEquipment(self::SLOT_WEAPON_A2);
        }
        if ($weaponA2) {
            return $weaponA2;
        } elseif (!\Arnapou\GW2Api\is_two_handed_weapon($weaponA1)) {
            if ($weaponB2 && !\Arnapou\GW2Api\is_two_handed_weapon($weaponB1)) {
                return $weaponB2;
            }
        }
        return null;
    }

    /**
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->attributes === null) {
            $profession = $this->getData('profession');
            $level      = $this->getLevel();
            $attributes = [
                Item::ATTRIBUTE_POWER             => 1000,
                Item::ATTRIBUTE_PRECISION         => 1000,
                Item::ATTRIBUTE_THOUGHNESS        => 1000,
                Item::ATTRIBUTE_VITALITY          => 1000,
                Item::ATTRIBUTE_CRITDAMAGE        => 0,
                Item::ATTRIBUTE_CONDITIONDAMAGE   => 0,
                Item::ATTRIBUTE_CONDITIONDURATION => 0,
                Item::ATTRIBUTE_HEALING           => 0,
                Item::ATTRIBUTE_BOONDURATION      => 0,
                Item::ATTRIBUTE_AGONYRESISTANCE   => 0,
                'Armor'                           => 0,
            ];
            $items      = [$this->getEquipmentWeapon1(), $this->getEquipmentWeapon2()];
            foreach ([
                         self::SLOT_HELM, self::SLOT_SHOULDERS, self::SLOT_COAT,
                         self::SLOT_GLOVES, self::SLOT_LEGGINGS, self::SLOT_BOOTS,
                         self::SLOT_AMULET, self::SLOT_ACCESSORY1, self::SLOT_ACCESSORY2,
                         self::SLOT_BACKPACK, self::SLOT_RING1, self::SLOT_RING2,
                     ] as $slot) {
                $items[] = $this->getEquipment($slot);
            }

            foreach ($items as $item) {
                if ($item) {
                    $attrs = $item->getAttributes();
                    foreach ($attrs as $attr => $value) {
                        if (isset($attributes[$attr])) {
                            $attributes[$attr] += (int)$value;
                        }
                    }
                    $attributes['Armor']                         += (int)$item->getArmorDefense();
                    $attributes[Item::ATTRIBUTE_AGONYRESISTANCE] += $item->getAgonyResistance();
                }
            }

            $attributes[Item::ATTRIBUTE_PRECISION . 'Pct']         = round(($attributes[Item::ATTRIBUTE_PRECISION] - 916) / 21, 2);
            $attributes[Item::ATTRIBUTE_CRITDAMAGE . 'Pct']        = round(150 + $attributes[Item::ATTRIBUTE_CRITDAMAGE] / 15, 2);
            $attributes[Item::ATTRIBUTE_CONDITIONDURATION . 'Pct'] = round($attributes[Item::ATTRIBUTE_CONDITIONDURATION] / 15, 2);
            $attributes[Item::ATTRIBUTE_BOONDURATION . 'Pct']      = round($attributes[Item::ATTRIBUTE_BOONDURATION] / 15, 2);

            /*
             * calculate heatlh
             */
            $healthMap            = [
                [
                    'professions' => [self::PROFESSION_WARRIOR, self::PROFESSION_NECROMANCER],
                    'levels'      => [
                        28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28,
                        70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70,
                        140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140, 140,
                        210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210, 210,
                        280,
                    ],
                ], [
                    'professions' => [self::PROFESSION_REVENANT, self::PROFESSION_ENGINEER, self::PROFESSION_RANGER, self::PROFESSION_MESMER],
                    'levels'      => [
                        18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18,
                        45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45,
                        90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90,
                        135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135, 135,
                        180,
                    ],
                ], [
                    'professions' => [self::PROFESSION_GUARDIAN, self::PROFESSION_THIEF, self::PROFESSION_ELEMENTALIST],
                    'levels'      => [
                        5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5,
                        12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5,
                        25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25,
                        37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5, 37.5,
                        50,
                    ],
                ],
            ];
            $attributes['Armor']  += $attributes[Item::ATTRIBUTE_THOUGHNESS];
            $attributes['Health'] = 0;
            foreach ($healthMap as $map) {
                if (in_array($profession, $map['professions'])) {
                    for ($i = 0; $i < $level; $i++) {
                        $attributes['Health'] += $map['levels'][$i];
                    }
                    break;
                }
            }
            $attributes['Health'] += 10 * $attributes[Item::ATTRIBUTE_VITALITY];

            $this->attributes = $attributes;
        }
        return $this->attributes;
    }
}
