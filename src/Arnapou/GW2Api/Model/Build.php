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
 * @method string  getType()
 */
class Build extends AbstractObject
{
    // TYPES
    const TYPE_PVE = 'pve';
    const TYPE_PVP = 'pvp';
    const TYPE_WVW = 'wvw';

    /**
     *
     * @var array
     */
    protected $specializations = [];

    /**
     *
     * @var array
     */
    protected $skills = [
        'heal'      => null,
        'utilities' => [],
        'elite'     => null,
        'legends'   => [],
        'pets'      => [
            'terrestrial' => [],
            'aquatic'     => [],
        ],
    ];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['specializations']) && \is_array($data['specializations'])) {
            foreach ($data['specializations'] as $spedata) {
                $this->specializations[] = new SpecializationLine($this->getEnvironment(), $spedata);
            }
        }
        if (isset($data['skills'])) {
            if (isset($data['skills']['heal'])) {
                $this->skills['heal'] = new Skill($this->getEnvironment(), $data['skills']['heal']);
            }
            if (isset($data['skills']['elite'])) {
                $this->skills['elite'] = new Skill($this->getEnvironment(), $data['skills']['elite']);
            }
            if (isset($data['skills']['utilities']) && \is_array($data['skills']['utilities'])) {
                foreach ($data['skills']['utilities'] as $id) {
                    $this->skills['utilities'][] = new Skill($this->getEnvironment(), $id);
                }
            }
            if (isset($data['skills']['legends']) && \is_array($data['skills']['legends'])) {
                foreach ($data['skills']['legends'] as $id) {
                    $this->skills['legends'][] = new Legend($this->getEnvironment(), $id);
                }
            }
            foreach ($this->skills['pets'] as $type => $tmp) {
                if (isset($data['skills']['pets'], $data['skills']['pets'][$type]) && \is_array($data['skills']['pets'][$type])) {
                    foreach ($data['skills']['pets'][$type] as $id) {
                        $this->skills['pets'][$type][] = new Pet($this->getEnvironment(), $id);
                    }
                }
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getProfession()
    {
        foreach ($this->specializations as $spe) {
            return $spe->getProfession();
        }
        return '';
    }

    /**
     *
     * @return SpecializationLine[]
     */
    public function getSpecializations()
    {
        return $this->specializations;
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
    public function getSkillHeal()
    {
        return $this->skills['heal'];
    }

    /**
     *
     * @return Skill
     */
    public function getSkillElite()
    {
        return $this->skills['elite'];
    }

    /**
     *
     * @return array
     */
    public function getSkillUtilities()
    {
        return $this->skills['utilities'];
    }

    /**
     *
     * @return array
     */
    public function getLegends()
    {
        return $this->skills['legends'];
    }

    /**
     *
     * @return array
     */
    public function getPets()
    {
        return $this->skills['pets'];
    }

    /**
     *
     * @return array
     */
    public function getPetsTerrestrial()
    {
        return $this->skills['pets']['terrestrial'];
    }

    /**
     *
     * @return array
     */
    public function getPetsAquatic()
    {
        return $this->skills['pets']['aquatic'];
    }
}
