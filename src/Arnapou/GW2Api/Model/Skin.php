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
class Skin extends AbstractObject {

    // TYPES
    const TYPE_ARMOR                   = 'Armor';
    const TYPE_BACK                    = 'Back';
    const TYPE_WEAPON                  = 'Weapon';
    // WEIGHT_CLASS
    const WEIGHT_CLASS_HEAVY           = 'Heavy';
    const WEIGHT_CLASS_MEDIUM          = 'Medium';
    const WEIGHT_CLASS_LIGHT           = 'Light';
    const WEIGHT_CLASS_CLOTHING        = 'Clothing';
    // WEAPON_DAMAGE_TYPE
    const WEAPON_DAMAGE_TYPE_FIRE      = 'Fire';
    const WEAPON_DAMAGE_TYPE_ICE       = 'Ice';
    const WEAPON_DAMAGE_TYPE_LIGHTNING = 'Lightning';
    const WEAPON_DAMAGE_TYPE_PHYSICAL  = 'Physical';
    const WEAPON_DAMAGE_TYPE_CHOKING   = 'Choking';

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
     * @param SimpleClient $client
     * @param array $id
     */
    public function __construct(SimpleClient $client, $id) {
        parent::__construct($client);

        $this->data = $this->apiSkins($id);
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
    public function getType() {
        return $this->getSubkey(['type']);
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
    public function getIcon() {
        return $this->getSubkey(['icon']);
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
    public function getWeaponDamageType() {
        return $this->getSubkey(['details', 'damage_type']);
    }

}
