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

use Arnapou\GW2Api\Core\AbstractClient;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Item extends AbstractObject {

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
    const ATTRIBUTE_FEROCITY                          = 'Ferocity';
    const ATTRIBUTE_THOUGHNESS                        = 'Toughness';
    const ATTRIBUTE_VITALITY                          = 'Vitality';
    const ATTRIBUTE_HEALING                           = 'Healing';
    const ATTRIBUTE_CONDITION                         = 'Condition';

    /**
     *
     * @var array
     */
    protected static $STATS = [
        'Power/Precision/Ferocity'               => "Berserker's",
        'Power/Ferocity/Precision'               => "Berserker's",
        'Power/Ferocity/Precision/Vitality'      => "Berserker's + Valkyrie",
        'Power/Healing/Precision'                => "Zealot's",
        'Power/Precision/Healing'                => "Zealot's",
        'Power/Toughness/Vitality'               => "Soldier's",
        'Power/Vitality/Toughness'               => "Soldier's",
        'Power/Ferocity/Vitality'                => "Valkyrie",
        'Power/Vitality/Ferocity'                => "Valkyrie",
        'Power/Toughness/Healing'                => "Forsaken",
        'Power/Healing/Toughness'                => "Forsaken",
        'Precision/Toughness/Power'              => "Captain's",
        'Precision/Power/Toughness'              => "Captain's",
        'Precision/Condition/Power'              => "Rampager's",
        'Precision/Power/Condition'              => "Rampager's",
        'Precision/Ferocity/Power'               => "Assassin's",
        'Precision/Power/Ferocity'               => "Assassin's",
        'Toughness/Precision/Power'              => "Knight's",
        'Toughness/Power/Precision'              => "Knight's",
        'Toughness/Power/Ferocity'               => "Cavalier's",
        'Toughness/Ferocity/Power'               => "Cavalier's",
        'Toughness/Healing/Vitality'             => "Nomad's",
        'Toughness/Vitality/Healing'             => "Nomad's",
        'Toughness/Healing/Condition'            => "Settler's",
        'Toughness/Condition/Healing'            => "Settler's",
        'Toughness/Healing'                      => "Giver's",
        'Healing/Toughness'                      => "Giver's",
        'Vitality/Toughness/Power'               => "Sentinel's",
        'Vitality/Toughness/Power'               => "Sentinel's",
        'Vitality/Healing/Condition'             => "Shaman's",
        'Vitality/Condition/Healing'             => "Shaman's",
        'Vitality/Healing/Power'                 => "of the shaman",
        'Vitality/Power/Healing'                 => "of the shaman",
        'Condition/Precision/Power'              => "Sinister",
        'Condition/Power/Precision'              => "Sinister",
        'Condition/Vitality/Power'               => "Carrion",
        'Condition/Power/Vitality'               => "Carrion",
        'Condition/Toughness/Precision'          => "Rabid",
        'Condition/Precision/Toughness'          => "Rabid",
        'Condition/Toughness/Precision/Healing'  => "Rabid + Apothecary's",
        'Condition/Vitality/Toughness'           => "Dire",
        'Condition/Toughness/Vitality'           => "Dire",
        'Condition/Toughness/Vitality/Precision' => "Dire + Rabid",
        'Condition/Healing/Toughness'            => "Apostate's",
        'Condition/Toughness/Healing'            => "Apostate's",
        'Healing/Toughness/Power'                => "Cleric's",
        'Healing/Power/Toughness'                => "Cleric's",
        'Healing/Vitality/Precision'             => "Magi's",
        'Healing/Precision/Vitality'             => "Magi's",
        'Healing/Condition/Toughness'            => "Apothecary's",
        'Healing/Toughness/Condition'            => "Apothecary's",
    ];

    /**
     *
     * @var Skin
     */
    protected $defaultSkin;

    /**
     *
     * @var array
     */
    protected $attributes;

    /**
     *
     * @var array
     */
    protected $price;

    /**
     * 
     * @param SimpleClient $client
     * @param array $id
     */
    public function __construct(SimpleClient $client, $id) {
        parent::__construct($client);

        $this->data = $this->apiItems($id);
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getSubkey(['name']);
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
        return $this->getSubkey(['icon']);
    }

    /**
     * 
     * @return string
     */
    public function getDescription() {
        return strip_tags($this->getSubkey(['description']));
    }

    /**
     * 
     * @return string
     */
    public function getType() {
        return $this->getSubkey(['type']);
    }

    /**
     * 
     * @return string
     */
    public function getRarity() {
        return $this->getSubkey(['rarity']);
    }

    /**
     * 
     * @return integer
     */
    public function getLevel() {
        return $this->getSubkey(['level']);
    }

    /**
     * 
     * @return integer
     */
    public function getVendorValue() {
        return $this->getSubkey(['vendor_value']);
    }

    /**
     * 
     * @return Skin
     */
    public function getDefaultSkin() {
        if (!isset($this->defaultSkin)) {
            $id = $this->getSubkey(['default_skin']);
            if ($id) {
                $this->defaultSkin = new Skin($this->client, $id);
            }
        }
        return $this->defaultSkin;
    }

    /**
     * 
     * @return string
     */
    public function getFlags() {
        return $this->getSubkey(['flags']);
    }

    /**
     * 
     * @param string $flag
     * @return boolean
     */
    public function hasFlag($flag) {
        $flags = $this->getFlags();
        if (is_array($flags)) {
            return in_array($flag, $flags);
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    public function getGameTypes() {
        return $this->getSubkey(['game_types']);
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public function hasGameType($type) {
        $types = $this->getGameTypes();
        if (is_array($types)) {
            return in_array($type, $types);
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    public function getRestrictions() {
        return $this->getSubkey(['restrictions']);
    }

    /**
     * 
     * @param string $restriction
     * @return boolean
     */
    public function hasRestriction($restriction) {
        $restrictions = $this->getGameTypes();
        if (is_array($restrictions)) {
            return in_array($restriction, $restrictions);
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    public function getSubType() {
        return $this->getSubkey(['details', 'type']);
    }

    /**
     * 
     * @return string
     */
    public function getArmorWeightClass() {
        return $this->getSubkey(['details', 'weight_class']);
    }

    /**
     * 
     * @return integer
     */
    public function getArmorDefense() {
        return $this->getSubkey(['details', 'defense']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponDamageType() {
        return $this->getSubkey(['details', 'damage_type']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponMinPower() {
        return $this->getSubkey(['details', 'min_power']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponMaxPower() {
        return $this->getSubkey(['details', 'max_power']);
    }

    /**
     * 
     * @return integer
     */
    public function getWeaponDefense() {
        return $this->getSubkey(['details', 'defense']);
    }

    /**
     * 
     * @return string
     */
    public function getConsumableDescription() {
        return $this->getSubkey(['details', 'description']);
    }

    /**
     * 
     * @return integer
     */
    public function getConsumableDurationMs() {
        return $this->getSubkey(['details', 'duration_ms']);
    }

    /**
     * The dye id for dyes
     * @return integer
     */
    public function getConsumableColorId() {
        return $this->getSubkey(['details', 'color_id']);
    }

    /**
     * The recipe id for recipe unlocks.
     * @return integer
     */
    public function getConsumableRecipeId() {
        return $this->getSubkey(['details', 'recipe_id']);
    }

    /**
     * 
     * @return string
     */
    public function getConsumableUnlockType() {
        return $this->getSubkey(['details', 'unlock_type']);
    }

    /**
     * 
     * @return integer
     */
    public function getSalvageCharges() {
        return $this->getSubkey(['details', 'charges']);
    }

    /**
     * 
     * @return array
     */
    public function getUpgradeComponentFlags() {
        return $this->getSubkey(['details', 'flags']);
    }

    /**
     * 
     * @return array
     */
    public function getUpgradeComponentSuffix() {
        return $this->getSubkey(['details', 'suffix']);
    }

    /**
     * 
     * @return array
     */
    public function getBagSize() {
        return $this->getSubkey(['details', 'size']);
    }

    /**
     * 
     * @return Item
     */
    public function getSuffixItem() {
        $id = $this->getSubkey(['details', 'suffix_item_id']);
        if ($id) {
            return new Item($this->client, $id);
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getBuffDescription() {
        $buff = $this->getSubkey(['details', 'infix_upgrade', 'buff', 'description']);
        if (empty($buff)) {
            $buff = $this->getSubkey(['details', 'bonuses']);
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
        return $this->getSubkey(['details', 'infix_upgrade', 'buff', 'skill_id']);
    }

    /**
     * 
     * @return string
     */
    public function getInfusionSlots() {
        return $this->getSubkey(['details', 'infusion_slots']);
    }

    /**
     * 
     * @return integer
     */
    public function getAgonyResistance() {
        if ($this->getType() !== self::TYPE_UPGRADE_COMPONENT) {
            return null;
        }
        $buffDescription = $this->getBuffDescription();
        if (!empty($buffDescription)) {
            if (preg_match('!\+([0-9]+)!i', $buffDescription, $m)) {
                return $m[1];
            }
        }
        return null;
    }

    /**
     * 
     * @return arrray
     */
    public function getAttributes() {
        if (!isset($this->attributes)) {
            $this->attributes = [];
            $attributes       = $this->getSubkey(['details', 'infix_upgrade', 'attributes']);
            if (is_array($attributes)) {
                usort($attributes, function($a, $b) {
                    if ($a['modifier'] == $b['modifier']) {
                        return 0;
                    }
                    return $a['modifier'] < $b['modifier'] ? 1 : -1;
                });
                $list = [];
                foreach ($attributes as $attribute) {
                    $name        = str_replace('CritDamage', 'Ferocity', $attribute['attribute']);
                    $name        = str_replace('ConditionDamage', 'Condition', $name);
                    $list[$name] = $attribute['modifier'];
                }
                if (count($attributes) >= 7) {
                    $stats      = 'Celestial';
                    $stats_name = 'Celestial';
                }
                else {
                    if ($this->getRarity() == self::RARITY_ASCENDED) {
                        $buff = $this->getBuffDescription();
                        if ($buff) {
                            $buff = $this->getItemEN()->getBuffDescription();
                            if ($buff) {
                                $stats_names = [
                                    'Power'            => 'Power',
                                    'Precision'        => 'Precision',
                                    'Toughness'        => 'Toughness',
                                    'Vitality'         => 'Vitality',
                                    'Ferocity'         => 'Ferocity',
                                    'Condition Damage' => 'Condition',
                                    'Healing'          => 'Healing',
                                ];
                                $lines       = explode("\n", $buff);
                                foreach ($lines as $line) {
                                    if (preg_match('!^\+([0-9]+) (.+)$!i', $line, $m)) {
                                        if (isset($stats_names[$m[2]])) {
                                            if (isset($list[$stats_names[$m[2]]])) {
                                                $list[$stats_names[$m[2]]] += $m[1];
                                            }
                                            else {
                                                $list[$stats_names[$m[2]]] = $m[1];
                                            }
                                        }
                                    }
                                }
                                uasort($list, function($a, $b) {
                                    if ($a == $b) {
                                        return 0;
                                    }
                                    return $a < $b ? 1 : -1;
                                });
                            }
                        }
                    }

                    $stats      = implode('/', array_keys($list));
                    $stats_name = isset(self::$STATS[$stats]) ? self::$STATS[$stats] : '';
                }
                $this->attributes = [
                    'stats'      => $stats,
                    'stats_name' => $stats_name,
                    'list'       => $list,
                ];
            }
        }
        return $this->attributes;
    }

    /**
     * 
     * @return Item
     */
    protected function getItemEN() {
        $lang = $this->client->getLang();
        if ($lang == AbstractClient::LANG_EN) {
            return $this;
        }
        $this->client->setLang(AbstractClient::LANG_EN);
        $object = new Item($this->client, $this->getId());
        $this->client->setLang($lang);
        return $object;
    }

    /**
     * 
     * @return array [buy: x, sell: y]
     */
    public function getPrice() {
        if (!isset($this->price)) {
            $price = $this->apiPrices($this->getId());

            $this->price = [
                'buy'  => isset($price['buys'], $price['buys']['unit_price']) ? $price['buys']['unit_price'] : null,
                'sell' => isset($price['sells'], $price['sells']['unit_price']) ? $price['sells']['unit_price'] : null,
            ];
        }
        return $this->price;
    }

}
