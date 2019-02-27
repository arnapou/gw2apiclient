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
 *
 * @method string getId()
 * @method string getSlot()
 * @method string getOffhand() The name of the offhand weapon this skill requires to be equipped. This field is usually
 *         only present for Thief skills.
 * @method string getAttunement() The Elementalist attunement that this skill requires. This field is usually only
 *         present for Elementalist skills.
 */
class ProfessionWeaponSkill extends AbstractObject
{
    const SLOT_WEAPON_1 = 'Weapon_1';
    const SLOT_WEAPON_2 = 'Weapon_2';
    const SLOT_WEAPON_3 = 'Weapon_3';
    const SLOT_WEAPON_4 = 'Weapon_4';
    const SLOT_WEAPON_5 = 'Weapon_5';

    /**
     *
     * @var Skill
     */
    protected $skill;

    /**
     *
     * @var Specialization
     */
    protected $specialization;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['id'])) {
            $this->skill = new Skill($this->getEnvironment(), $data['id']);
        }
    }

    /**
     *
     * @return Skill
     */
    public function getSkill()
    {
        return $this->skill;
    }
}
