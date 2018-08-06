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
 * @doc https://wiki.guildwars2.com/wiki/API:2/specializations
 *
 * @method getName()
 * @method getIcon()
 * @method getDescription()
 * @method getTier()
 * @method getSlot()
 */
class SpecializationTrait extends AbstractStoredObject
{
    const SLOT_MAJOR = 'Major';
    const SLOT_MINOR = 'Minor';

    /**
     *
     * @var array
     */
    protected $skills = [];

    /**
     *
     * @return array
     */
    public function getSkills()
    {
        $this->checkLoadData();
        return $this->skills;
    }

    /**
     *
     * @return array
     */
    public function getFacts()
    {
        $this->checkLoadData();
        return $this->getData('facts', []);
    }

    /**
     *
     * @return array
     */
    public function getTraitedFacts()
    {
        $this->checkLoadData();
        return $this->getData('traited_facts', []);
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['skills']) && is_array($data['skills'])) {
            foreach ($data['skills'] as $item) {
                if (isset($item['id'])) {
                    $this->skills[] = new Skill($this->getEnvironment(), $item['id']);
                }
            }
        }
    }

    public function getApiName()
    {
        return 'traits';
    }
}
