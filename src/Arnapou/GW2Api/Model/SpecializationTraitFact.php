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
class SpecializationTraitFact extends AbstractObject {

    // TYPES
    const TYPE_ATTRIBUTE_ADJUST    = 'AttributeAdjust';
    const TYPE_BUFF                = 'Buff';
    const TYPE_BUFF_CONVERSION     = 'BuffConversion';
    const TYPE_COMBO_FIELD         = 'ComboField';
    const TYPE_COMBO_FINISHER      = 'ComboFinisher';
    const TYPE_DAMAGE              = 'Damage';
    const TYPE_DISTANCE            = 'Distance';
    const TYPE_NO_DATA             = 'NoData';
    const TYPE_NUMBER              = 'Number';
    const TYPE_PERCENT             = 'Percent';
    const TYPE_PREFIXED_BUFF       = 'PrefixedBuff';
    const TYPE_RADIUS              = 'Radius';
    const TYPE_RANGE               = 'Range';
    const TYPE_RECHARGE            = 'Recharge';
    const TYPE_TIME                = 'Time';
    const TYPE_UNBLOCKABLE         = 'Unblockable';
    // FIELD_TYPES
    const FIELD_TYPE_AIR           = 'Air';
    const FIELD_TYPE_DARK          = 'Dark';
    const FIELD_TYPE_FIRE          = 'Fire';
    const FIELD_TYPE_ICE           = 'Ice';
    const FIELD_TYPE_LIGHT         = 'Light';
    const FIELD_TYPE_LIGHTNING     = 'Lightning';
    const FIELD_TYPE_POISON        = 'Poison';
    const FIELD_TYPE_SMOKE         = 'Smoke';
    const FIELD_TYPE_ETHEREAL      = 'Ethereal';
    const FIELD_TYPE_WATER         = 'Water';
    // FINISHER_TYPES
    const FINISHER_TYPE_BLAST      = 'Blast';
    const FINISHER_TYPE_LEAP       = 'Leap';
    const FINISHER_TYPE_PROJECTILE = 'Projectile';
    const FINISHER_TYPE_WHIRL      = 'Whirl';

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
    }

    /**
     * 
     * @return string
     */
    public function getText() {
        return $this->getSubkey(['text']);
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
    public function getType() {
        return $this->data['type'];
    }

    /**
     * Types :
     * - ComboField
     * 
     * @return string
     */
    public function getFieldType() {
        return $this->getSubkey(['field_type']);
    }

    /**
     * Types :
     * - AttributeAdjust
     * - BuffConversion
     * 
     * @return string
     */
    public function getTarget() {
        $s = $this->getSubkey(['target']);
        $s = str_replace('CritDamage', 'Ferocity', $s);
        $s = str_replace('ConditionDamage', 'Condition', $s);
        return $s;
    }

    /**
     * Types :
     * - AttributeAdjust
     * - Number
     * - Range
     * - Recharge
     * - Unblockable
     * 
     * @return int
     */
    public function getValue() {
        return $this->getSubkey(['value']);
    }

    /**
     * Types :
     * - Buff
     * 
     * @return string
     */
    public function getStatus() {
        return $this->getSubkey(['status']);
    }

    /**
     * Types :
     * - BuffConversion
     * 
     * @return string
     */
    public function getSource() {
        $s = $this->getSubkey(['source']);
        $s = str_replace('CritDamage', 'Ferocity', $s);
        $s = str_replace('ConditionDamage', 'Condition', $s);
        return $s;
    }

    /**
     * Types :
     * - PrefixedBuff
     * 
     * @return string
     */
    public function getPrefix() {
        return $this->getSubkey(['prefix']);
    }

    /**
     * Types :
     * - BuffConversion
     * - ComboFinisher
     * - Percent
     * 
     * @return string
     */
    public function getPercent() {
        return $this->getSubkey(['percent']);
    }

    /**
     * Types :
     * - Buff
     * 
     * @return string
     */
    public function getDescription() {
        return $this->getSubkey(['description']);
    }

    /**
     * Types :
     * - Buff
     * 
     * @return int
     */
    public function getApplyCount() {
        return $this->getSubkey(['apply_count']);
    }

    /**
     * Types :
     * - Damage
     * 
     * @return int
     */
    public function getHitCount() {
        return $this->getSubkey(['hit_count']);
    }

    /**
     * Types :
     * - Distance
     * - Radius
     * 
     * @return int
     */
    public function getDistance() {
        return $this->getSubkey(['distance']);
    }

    /**
     * Types :
     * - Buff
     * - Time
     * 
     * @return int
     */
    public function getDuration() {
        return $this->getSubkey(['duration']);
    }

}
