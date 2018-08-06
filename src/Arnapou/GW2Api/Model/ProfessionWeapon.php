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
 * @method string getType()
 */
class ProfessionWeapon extends AbstractObject
{
    /**
     *
     * @var array
     */
    protected $skills = [];

    /**
     *
     * @var Specialization
     */
    protected $specialization;

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();
        if (isset($data['skills']) && is_array($data['skills'])) {
            foreach ($data['skills'] as $item) {
                $this->skills[] = new ProfessionWeaponSkill($env, $item);
            }
            usort($this->skills, function ($a, $b) {
                $ret = ((string)$a->getAttunement()) <=> ($b->getAttunement());
                if ($ret == 0) {
                    return $a->getSlot() <=> $b->getSlot();
                }
                return $ret;
            });
        }

        if (isset($data['specialization'])) {
            $this->specialization = new Specialization($env, $data['specialization']);
        }
    }

    /**
     *
     * @return array
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     *
     * @return Specialization
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }
}
