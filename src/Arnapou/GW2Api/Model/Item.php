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

use Arnapou\GW2Api\Environment;

/**
 * @doc https://wiki.guildwars2.com/wiki/API:2/items
 * 
 * @method string getName()
 * @method string getIcon()
 * @method string getDescription()
 * @method string getType()
 * @method string getRarity()
 * @method string getLevel()
 * @method string getVendorValue()
 */
class Item extends AbstractStoredObject {

    // TYPES
    const TYPE_ARMOR                                  = 'Armor';
    const TYPE_BACK                                   = 'Back';
    const TYPE_BAG                                    = 'Bag';
    const TYPE_CONSUMABLE                             = 'Consumable';
    const TYPE_CONTAINER                              = 'Container';
    const TYPE_CRAFTING_MATERIAL                      = 'CraftingMaterial';
    const TYPE_GATHERING                              = 'Gathering';
    const TYPE_GIZMO                                  = 'Gizmo';
    const TYPE_MINIPET                                = 'MiniPet';
    const TYPE_TOOL                                   = 'Tool';
    const TYPE_TRAIT                                  = 'Trait';
    const TYPE_TRINKET                                = 'Trinket';
    const TYPE_TROPHY                                 = 'Trophy';
    const TYPE_UPGRADE_COMPONENT                      = 'UpgradeComponent';
    const TYPE_WEAPON                                 = 'Weapon';
    // RARITIES
    const RARITY_JUNK                                 = 'Junk';
    const RARITY_BASIC                                = 'Basic';
    const RARITY_FINE                                 = 'Fine';
    const RARITY_MASTERWORK                           = 'Masterwork';
    const RARITY_RARE                                 = 'Rare';
    const RARITY_EXOTIC                               = 'Exotic';
    const RARITY_ASCENDED                             = 'Ascended';
    const RARITY_LEGENDARY                            = 'Legendary';
    // FLAGS
    const FLAG_ACCOUNT_BIND_ON_USE                    = 'AccountBindOnUse';
    const FLAG_ACCOUNT_BOUND                          = 'AccountBound';
    const FLAG_HIDE_SUFFIX                            = 'HideSuffix';
    const FLAG_MONSTER_ONLY                           = 'MonsterOnly';
    const FLAG_NO_MYSTIC_FORGE                        = 'NoMysticForge';
    const FLAG_NO_SALVAGE                             = 'NoSalvage';
    const FLAG_NO_SELL                                = 'NoSell';
    const FLAG_NOT_UPGRADEABLE                        = 'NotUpgradeable';
    const FLAG_NO_UNDERWATER                          = 'NoUnderwater';
    const FLAG_SOUL_BIND_ON_ACQUIRE                   = 'SoulbindOnAcquire';
    const FLAG_SOUL_BIND_ON_USE                       = 'SoulBindOnUse';
    const FLAG_UNIQUE                                 = 'Unique';
    // GAME_TYPES
    const GAME_TYPE_ACTIVITY                          = 'Activity';
    const GAME_TYPE_DUNGEON                           = 'Dungeon';
    const GAME_TYPE_PVE                               = 'Pve';
    const GAME_TYPE_PVP                               = 'Pvp';
    const GAME_TYPE_PVB_LOBBY                         = 'PvpLobby';
    const GAME_TYPE_WVW                               = 'Wvw';
    // RESTRICTIONS
    const RESTRICTIONS_ASURA                          = 'Asura';
    const RESTRICTIONS_CHARR                          = 'Charr';
    const RESTRICTIONS_HUMAN                          = 'Human';
    const RESTRICTIONS_NORN                           = 'Norn';
    const RESTRICTIONS_SYLVARI                        = 'Sylvari';
    const RESTRICTIONS_ELEMENTALIST                   = 'Elementalist';
    const RESTRICTIONS_ENGINEER                       = 'Engineer';
    const RESTRICTIONS_GUARDIAN                       = 'Guardian';
    const RESTRICTIONS_MESMER                         = 'Mesmer';
    const RESTRICTIONS_NECROMANCER                    = 'Necromancer';
    const RESTRICTIONS_RANGER                         = 'Ranger';
    const RESTRICTIONS_THIEF                          = 'Thief';
    const RESTRICTIONS_WARRIOR                        = 'Warrior';
    const RESTRICTIONS_REVENANT                       = 'Revenant';
    // SUBTYPES
    const SUBTYPE_ARMOR_HELM                          = 'Helm';
    const SUBTYPE_ARMOR_HELM_AQUATIC                  = 'HelmAquatic';
    const SUBTYPE_ARMOR_SHOULDERS                     = 'Shoulders';
    const SUBTYPE_ARMOR_COAT                          = 'Coat';
    const SUBTYPE_ARMOR_GLOVES                        = 'Gloves';
    const SUBTYPE_ARMOR_LEGGINGS                      = 'Leggings';
    const SUBTYPE_ARMOR_BOOTS                         = 'Boots';
    const SUBTYPE_CONSUMABLE_APPEARANCE_CHANGE        = 'AppearanceChange';
    const SUBTYPE_CONSUMABLE_BOOZE                    = 'Booze';
    const SUBTYPE_CONSUMABLE_CONTRACT_NPC             = 'ContractNpc';
    const SUBTYPE_CONSUMABLE_FOOD                     = 'Food';
    const SUBTYPE_CONSUMABLE_GENERIC                  = 'Generic';
    const SUBTYPE_CONSUMABLE_HALLOWEEN                = 'Halloween';
    const SUBTYPE_CONSUMABLE_IMMEDIATE                = 'Immediate';
    const SUBTYPE_CONSUMABLE_TRANSMUTATION            = 'Transmutation';
    const SUBTYPE_CONSUMABLE_UNLOCK                   = 'Unlock';
    const SUBTYPE_CONSUMABLE_UPGRADE_REMOVAL          = 'UpgradeRemoval';
    const SUBTYPE_CONSUMABLE_UTILITY                  = 'Utility';
    const SUBTYPE_CONTAINER_DEFAULT                   = 'Default';
    const SUBTYPE_CONTAINER_GIFTBOX                   = 'GiftBox';
    const SUBTYPE_CONTAINER_OPENUI                    = 'OpenUI';
    const SUBTYPE_GATHERING_FORAGING                  = 'Foraging';
    const SUBTYPE_GATHERING_LOGGING                   = 'Logging';
    const SUBTYPE_GATHERING_MINING                    = 'Mining';
    const SUBTYPE_GIZMO_DEFAULT                       = 'Default';
    const SUBTYPE_GIZMO_CONTAINER_KEY                 = 'ContainerKey';
    const SUBTYPE_GIZMO_RENTABLE_CONTRACT_NPC         = 'RentableContractNpc';
    const SUBTYPE_GIZMO_UNLIMITED_CONSMABLE           = 'UnlimitedConsumable';
    const SUBTYPE_TOOL_SALVAGE                        = 'Salvage';
    const SUBTYPE_TRINKET_AMULET                      = 'Amulet';
    const SUBTYPE_TRINKET_ACCESSORY                   = 'Accessory';
    const SUBTYPE_TRINKET_RING                        = 'Ring';
    const SUBTYPE_UPGRADE_COMPONENT_DEFAULT           = 'Default';
    const SUBTYPE_UPGRADE_COMPONENT_GEM               = 'Gem';
    const SUBTYPE_UPGRADE_COMPONENT_RUNE              = 'Rune';
    const SUBTYPE_UPGRADE_COMPONENT_SIGIL             = 'Sigil';
    const SUBTYPE_WEAPON_AXE                          = 'Axe';
    const SUBTYPE_WEAPON_DAGGER                       = 'Dagger';
    const SUBTYPE_WEAPON_MACE                         = 'Mace';
    const SUBTYPE_WEAPON_PISTOL                       = 'Pistol';
    const SUBTYPE_WEAPON_SCEPTER                      = 'Scepter';
    const SUBTYPE_WEAPON_SWORD                        = 'Sword';
    const SUBTYPE_WEAPON_FOCUS                        = 'Focus';
    const SUBTYPE_WEAPON_SHIELD                       = 'Shield';
    const SUBTYPE_WEAPON_TORCH                        = 'Torch';
    const SUBTYPE_WEAPON_WARHORN                      = 'Warhorn';
    const SUBTYPE_WEAPON_GREATSWORD                   = 'Greatsword';
    const SUBTYPE_WEAPON_HAMMER                       = 'Hammer';
    const SUBTYPE_WEAPON_LONGBOW                      = 'LongBow';
    const SUBTYPE_WEAPON_SHORTBOW                     = 'ShortBow';
    const SUBTYPE_WEAPON_RIFLE                        = 'Rifle';
    const SUBTYPE_WEAPON_STAFF                        = 'Staff';
    const SUBTYPE_WEAPON_HARPOON                      = 'Harpoon';
    const SUBTYPE_WEAPON_SPEARGUN                     = 'Speargun';
    const SUBTYPE_WEAPON_TRIDENT                      = 'Trident';
    const SUBTYPE_WEAPON_LARGE_BUNDLE                 = 'LargeBundle';
    const SUBTYPE_WEAPON_SMALL_BUNDLE                 = 'SmallBundle';
    const SUBTYPE_WEAPON_TOY                          = 'Toy';
    const SUBTYPE_WEAPON_TWO_HANDED_TOY               = 'TwoHandedToy';
    // CONSUMABLE_UNLOCK_TYPES
    const CONSUMABLE_UNLOCK_TYPE_BAG_SLOT             = 'BagSlot';
    const CONSUMABLE_UNLOCK_TYPE_BANK_TAB             = 'BankTab';
    const CONSUMABLE_UNLOCK_TYPE_COLLECTIBLE_CAPACITY = 'CollectibleCapacity';
    const CONSUMABLE_UNLOCK_TYPE_CONTENT              = 'Content';
    const CONSUMABLE_UNLOCK_TYPE_CRAFTING_RECIPE      = 'CraftingRecipe';
    const CONSUMABLE_UNLOCK_TYPE_DYE                  = 'Dye';
    const CONSUMABLE_UNLOCK_TYPE_UNKNOWN              = 'Unknown';
    // WEIGHT_CLASS
    const WEIGHT_CLASS_HEAVY                          = 'Heavy';
    const WEIGHT_CLASS_MEDIUM                         = 'Medium';
    const WEIGHT_CLASS_LIGHT                          = 'Light';
    const WEIGHT_CLASS_CLOTHING                       = 'Clothing';
    // WEAPON_DAMAGE_TYPE
    const WEAPON_DAMAGE_TYPE_FIRE                     = 'Fire';
    const WEAPON_DAMAGE_TYPE_ICE                      = 'Ice';
    const WEAPON_DAMAGE_TYPE_LIGHTNING                = 'Lightning';
    const WEAPON_DAMAGE_TYPE_PHYSICAL                 = 'Physical';
    const WEAPON_DAMAGE_TYPE_CHOKING                  = 'Choking';
    // ATTRIBUTES
    const ATTRIBUTE_POWER                             = 'Power';
    const ATTRIBUTE_PRECISION                         = 'Precision';
    const ATTRIBUTE_THOUGHNESS                        = 'Toughness';
    const ATTRIBUTE_VITALITY                          = 'Vitality';
    const ATTRIBUTE_HEALING                           = 'Healing';
    const ATTRIBUTE_AGONYRESISTANCE                   = 'AgonyResistance';
    const ATTRIBUTE_BOONDURATION                      = 'BoonDuration';      // concentration
    const ATTRIBUTE_CONDITIONDAMAGE                   = 'ConditionDamage';
    const ATTRIBUTE_CONDITIONDURATION                 = 'ConditionDuration'; // expertise
    const ATTRIBUTE_CRITDAMAGE                        = 'CritDamage';        // ferocity

    /**
     *
     * @var Skin
     */

    protected $defaultSkin = null;

    /**
     *
     * @var ItemStat
     */
    protected $itemStat = null;

    /**
     *
     * @var array
     */
    protected $price = null;

    public function __construct(Environment $environment, $id) {
        parent::__construct($environment, $id);

        if ($this->objectId) {
            $this->getEnvironment()->getStorage()->prepare(Environment::LANG_EN, 'prices', (string) $this->objectId);
        }
    }

    /**
     * 
     */
    public function getChatLink() {
        if ($this->getId()) {
            return \Arnapou\GW2Api\chatlink_item($this->getId());
        }
        return '';
    }

    /**
     * 
     * @return array [buy: x, sell: y]
     */
    public function getPrice() {
        if (empty($this->price)) {
            $this->price = [
                'buy'  => null,
                'sell' => null,
            ];
            if (!$this->hasFlag(self::FLAG_ACCOUNT_BOUND)) {
                if ($this->objectId) {
                    $env     = $this->getEnvironment();
                    $storage = $env->getStorage();
                    $client  = $env->getClientVersion2();
                    $data    = $storage->get(Environment::LANG_EN, 'prices', (string) $this->objectId, [$client, 'apiCommercePrices']);

                    if (isset($data['buys'], $data['buys']['unit_price'])) {
                        $this->price['buy'] = $data['buys']['unit_price'];
                    }
                    if (isset($data['sells'], $data['sells']['unit_price'])) {
                        $this->price['sell'] = $data['sells']['unit_price'];
                    }
                }
            }
        }
        return $this->price;
    }

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['default_skin'])) {
            $this->defaultSkin = new Skin($this->getEnvironment(), $data['default_skin']);
        }
        if (isset($data['infix_upgrade'], $data['infix_upgrade']['id'])) {
            $this->itemStat = new ItemStat($this->getEnvironment(), $data['infix_upgrade']['id']);
        }
    }

    /**
     * 
     * @return Skin
     */
    public function getDefaultSkin() {
        return $this->defaultSkin;
    }

    /**
     * 
     * @return array
     */
    public function getFlags() {
        return $this->getData('flags', []);
    }

    /**
     * 
     * @param string $flag
     * @return boolean
     */
    public function hasFlag($flag) {
        return in_array($flag, (array) $this->getFlags());
    }

    /**
     * 
     * @return array
     */
    public function getGameTypes() {
        return $this->getData('game_types', []);
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public function hasGameType($type) {
        return in_array($type, (array) $this->getGameTypes());
    }

    /**
     * 
     * @return string
     */
    public function getRestrictions() {
        return $this->getData('restrictions', []);
    }

    /**
     * 
     * @param string $restriction
     * @return boolean
     */
    public function hasRestriction($restriction) {
        return in_array($restriction, (array) $this->getRestrictions());
    }

    /**
     * 
     * @return string
     */
    public function getSubType() {
        return $this->getData(['details', 'type']);
    }

    /**
     * 
     * @return string
     */
    public function getArmorWeightClass() {
        return $this->getData(['details', 'weight_class']);
    }

    /**
     * 
     * @return integer
     */
    public function getArmorDefense() {
        return $this->getData(['details', 'defense']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponDamageType() {
        return $this->getData(['details', 'damage_type']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponMinPower() {
        return $this->getData(['details', 'min_power']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponMaxPower() {
        return $this->getData(['details', 'max_power']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponDefense() {
        return $this->getData(['details', 'defense']);
    }

    /**
     * 
     * @return string
     */
    public function getConsumableDescription() {
        return $this->getData(['details', 'description']);
    }

    /**
     * 
     * @return integer
     */
    public function getConsumableDurationMs() {
        return $this->getData(['details', 'duration_ms']);
    }

    /**
     * The dye id for dyes
     * @return integer
     */
    public function getConsumableColorId() {
        return $this->getData(['details', 'color_id']);
    }

    /**
     * The recipe id for recipe unlocks.
     * @return integer
     */
    public function getConsumableRecipeId() {
        return $this->getData(['details', 'recipe_id']);
    }

    /**
     * 
     * @return string
     */
    public function getConsumableUnlockType() {
        return $this->getData(['details', 'unlock_type']);
    }

    /**
     * 
     * @return integer
     */
    public function getSalvageCharges() {
        return $this->getData(['details', 'charges']);
    }

    /**
     * 
     * @return array
     */
    public function getUpgradeComponentFlags() {
        return $this->getData(['details', 'flags']);
    }

    /**
     * 
     * @return array
     */
    public function getUpgradeComponentSuffix() {
        return $this->getData(['details', 'suffix']);
    }

    /**
     * 
     * @return array
     */
    public function getBagSize() {
        return $this->getData(['details', 'size']);
    }

    /**
     * 
     * @return Item
     */
    public function getSuffixItemId() {
        return $this->getData(['details', 'suffix_item_id']);
    }

    /**
     * 
     * @return array
     */
    public function getAttributes() {
        $attributes = [];
        $array      = $this->getData(['details', 'infix_upgrade', 'attributes']);
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value) && isset($value['attribute'], $value['modifier'])) {
                    $attributes[$value['attribute']] = $value['modifier'];
                }
                else {
                    $attributes[$key] = $value;
                }
            }
        }
        return $attributes;
    }

    /**
     * 
     * @return ItemStat
     */
    public function getItemStat() {
        return $this->itemStat;
    }

    /**
     * 
     * @return string
     */
    public function getStatName() {
        if ($this->itemStat) {
            return $this->itemStat->getStatName();
        }
        return \Arnapou\GW2Api\attributes_to_statname($this->getAttributes());
    }

    /**
     * 
     * @return integer
     */
    public function getAgonyResistance() {
        if ($this->getType() !== self::TYPE_UPGRADE_COMPONENT ||
            $this->getSubType() !== self::SUBTYPE_UPGRADE_COMPONENT_DEFAULT
        ) {
            return null;
        }
        $attributes = $this->getAttributes();
        if (isset($attributes[self::ATTRIBUTE_AGONYRESISTANCE])) {
            return $attributes[self::ATTRIBUTE_AGONYRESISTANCE];
        }
        $buffDescription = $this->getBuffDescription();
        if (!empty($buffDescription)) {
            $lang = $this->getEnvironment()->getLang();

            if ($lang == Environment::LANG_ES && preg_match('!^\+([0-9]+) resistencia!i', $buffDescription, $m)) {
                return $m[1];
            }
            if ($lang == Environment::LANG_FR && preg_match('!agonie \+([0-9]+)$!i', $buffDescription, $m)) {
                return $m[1];
            }
            if ($lang == Environment::LANG_EN && preg_match('!^\+([0-9]+) agony!i', $buffDescription, $m)) {
                return $m[1];
            }
            if ($lang == Environment::LANG_DE && preg_match('!^\+([0-9]+) Qual-Widerstand!i', $buffDescription, $m)) {
                return $m[1];
            }
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getBuffDescription() {
        $buff = $this->getData(['details', 'infix_upgrade', 'buff', 'description']);
        if (empty($buff)) {
            $buff = $this->getData(['details', 'bonuses']);
        }
        if (is_array($buff)) {
            return implode("\n", $buff);
        }
        return $buff;
    }

    /**
     * 
     * @return string
     */
    public function getBuffSkillId() {
        return $this->getData(['details', 'infix_upgrade', 'buff', 'skill_id']);
    }

    /**
     * 
     * @return string
     */
    public function getInfusionSlots() {
        return $this->getData(['details', 'infusion_slots']);
    }

    /**
     * 
     * @return array
     */
    public function getStatChoices() {
        return $this->getData(['details', 'stat_choices'], []);
    }

    public function getApiName() {
        return 'items';
    }

}
