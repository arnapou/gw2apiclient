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
 * @doc https://wiki.guildwars2.com/wiki/API:2/skins
 *
 * @method string getName()
 * @method string getType()
 * @method string getRarity()
 * @method string getDescription()
 * @method string getIcon()
 */
class Skin extends AbstractStoredObject
{
    use UnlockTrait;

    // TYPES
    const TYPE_ARMOR  = 'Armor';
    const TYPE_BACK   = 'Back';
    const TYPE_WEAPON = 'Weapon';
    // WEIGHT_CLASS
    const WEIGHT_CLASS_HEAVY    = 'Heavy';
    const WEIGHT_CLASS_MEDIUM   = 'Medium';
    const WEIGHT_CLASS_LIGHT    = 'Light';
    const WEIGHT_CLASS_CLOTHING = 'Clothing';
    // FLAGS
    const FLAG_SHOW_IN_WARDROBE = 'ShowInWardrobe';
    const FLAG_NO_COST          = 'NoCost';
    const FLAG_HIDE_IF_LOCKED   = 'HideIfLocked';
    // WEAPON_DAMAGE_TYPE
    const WEAPON_DAMAGE_TYPE_FIRE      = 'Fire';
    const WEAPON_DAMAGE_TYPE_ICE       = 'Ice';
    const WEAPON_DAMAGE_TYPE_LIGHTNING = 'Lightning';
    const WEAPON_DAMAGE_TYPE_PHYSICAL  = 'Physical';
    const WEAPON_DAMAGE_TYPE_CHOKING   = 'Choking';

    /**
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->getData('flags', []);
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return \in_array($flag, (array)$this->getFlags());
    }

    /**
     *
     * @return string
     */
    public function getRestrictions()
    {
        return $this->getData('restrictions', []);
    }

    /**
     *
     * @param string $restriction
     * @return bool
     */
    public function hasRestriction($restriction)
    {
        return \in_array($restriction, (array)$this->getRestrictions());
    }

    /**
     *
     * @return string
     */
    public function getSubType()
    {
        return $this->getData(['details', 'type']);
    }

    /**
     *
     * @return string
     */
    public function getArmorWeightClass()
    {
        return $this->getData(['details', 'weight_class']);
    }

    /**
     *
     * @return int
     */
    public function getWeaponDamageType()
    {
        return $this->getData(['details', 'damage_type']);
    }

    public function getApiName()
    {
        return 'skins';
    }
}
