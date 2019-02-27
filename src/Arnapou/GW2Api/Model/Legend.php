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

class Legend extends AbstractStoredObject
{
    /**
     *
     * @var array
     */
    protected $skills = [
        'swap'      => null,
        'heal'      => null,
        'utilities' => [],
        'elite'     => null,
    ];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['swap'])) {
            $this->skills['swap'] = new Skill($this->getEnvironment(), $data['swap']);
        }
        if (isset($data['heal'])) {
            $this->skills['heal'] = new Skill($this->getEnvironment(), $data['heal']);
        }
        if (isset($data['elite'])) {
            $this->skills['elite'] = new Skill($this->getEnvironment(), $data['elite']);
        }
        if (isset($data['utilities']) && \is_array($data['utilities'])) {
            foreach ($data['utilities'] as $id) {
                $this->skills['utilities'][] = new Skill($this->getEnvironment(), $id);
            }
        }
    }

    /**
     *
     * @return Skill
     */
    public function getSkill($number)
    {
        if ($number == 6) {
            return $this->getSkillHeal();
        } elseif ($number >= 7 and $number <= 9) {
            $utilities = $this->getSkillUtilities();
            $index     = $number - 7;
            return $utilities[$index] ?? null;
        } elseif ($number == 0) {
            return $this->getSkillElite();
        }
        return null;
    }

    /**
     *
     * @return Skill
     */
    public function getSkillSwap()
    {
        $this->checkLoadData();
        return $this->skills['swap'];
    }

    /**
     *
     * @return Skill
     */
    public function getSkillHeal()
    {
        $this->checkLoadData();
        return $this->skills['heal'];
    }

    /**
     *
     * @return Skill
     */
    public function getSkillElite()
    {
        $this->checkLoadData();
        return $this->skills['elite'];
    }

    /**
     *
     * @return array
     */
    public function getSkillUtilities()
    {
        $this->checkLoadData();
        return $this->skills['utilities'];
    }

    public function getApiName()
    {
        return 'legends';
    }
}
