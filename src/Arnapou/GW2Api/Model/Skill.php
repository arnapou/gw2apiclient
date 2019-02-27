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
 * @doc https://wiki.guildwars2.com/wiki/API:2/skills
 *
 * @method getAttunement()
 * @method getChatLink()
 * @method getCost()
 * @method getDescription()
 * @method getDualWield()
 * @method getIcon()
 * @method getInitiative()
 * @method getName()
 * @method getSlot()
 * @method getType()
 * @method getWeaponType()
 */
class Skill extends AbstractStoredObject
{
    // TYPES
    const TYPE_BUNDLE     = 'Bundle';
    const TYPE_ELITE      = 'Elite';
    const TYPE_HEAL       = 'Heal';
    const TYPE_PROFESSION = 'Profession';
    const TYPE_UTILITY    = 'Utility';
    const TYPE_WEAPON     = 'Weapon';
    // CATEGORIES
    const CATEGORY_TRANSFORM      = 'Transform';
    const CATEGORY_DUAL_WIELD     = 'DualWield';
    const CATEGORY_STEALTH_ATTACK = 'StealthAttack';
    // ATTUNEMENT
    const ATTUNEMENT_FIRE  = 'Fire';
    const ATTUNEMENT_WATER = 'Water';
    const ATTUNEMENT_AIR   = 'Air';
    const ATTUNEMENT_EARTH = 'Earth';

    /**
     *
     * @var Skill
     */
    protected $flipSkill = null;

    /**
     *
     * @var Skill
     */
    protected $toolbeltSkill = null;

    /**
     *
     * @var Skill
     */
    protected $prevChain = null;

    /**
     *
     * @var Skill
     */
    protected $nextChain = null;

    /**
     *
     * @var array
     */
    protected $transformSkills = [];

    /**
     *
     * @var array
     */
    protected $bundleSkills = [];

    /**
     *
     * @return string
     */
    public function getPrevChainId()
    {
        return $this->getData('prev_chain');
    }

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
     * @return Skill
     */
    public function getPrevChain()
    {
        $this->checkLoadData();
        return $this->prevChain;
    }

    /**
     *
     * @return string
     */
    public function getNextChainId()
    {
        return $this->getData('next_chain');
    }

    /**
     *
     * @return Skill
     */
    public function getNextChain()
    {
        $this->checkLoadData();
        return $this->nextChain;
    }

    /**
     *
     * @return string
     */
    public function getFlipSkillId()
    {
        return $this->getData('flip_skill');
    }

    /**
     *
     * @return Skill
     */
    public function getFlipSkill()
    {
        $this->checkLoadData();
        return $this->flipSkill;
    }

    /**
     *
     * @return string
     */
    public function getToolbeltSkillId()
    {
        return $this->getData('toolbelt_skill');
    }

    /**
     *
     * @return Skill
     */
    public function getToolbeltSkill()
    {
        $this->checkLoadData();
        return $this->toolbeltSkill;
    }

    /**
     *
     * @return string
     */
    public function getTransformSkillsIds()
    {
        return $this->getData('transform_skills');
    }

    /**
     *
     * @return array
     */
    public function getTransformSkills()
    {
        $this->checkLoadData();
        return $this->transformSkills;
    }

    /**
     *
     * @return string
     */
    public function getBundleSkillsIds()
    {
        return $this->getData('bundle_skills');
    }

    /**
     *
     * @return array
     */
    public function getBundleSkills()
    {
        $this->checkLoadData();
        return $this->bundleSkills;
    }

    /**
     *
     * @return array
     */
    public function getProfessions()
    {
        return $this->getData('professions', []);
    }

    /**
     *
     * @return array
     */
    public function getFacts()
    {
        return $this->getData('facts', []);
    }

    /**
     *
     * @return array
     */
    public function getTraitedFacts()
    {
        return $this->getData('traited_facts', []);
    }

    /**
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->getData('categories', []);
    }

    /**
     *
     * @param int $index
     * @return string
     */
    public function getCategory($index = 0)
    {
        $categories = $this->getCategories();
        return isset($categories[$index]) ? $categories[$index] : null;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['flip_skill'])) {
            $this->flipSkill = new Skill($this->getEnvironment(), $data['flip_skill']);
        }
        if (isset($data['toolbelt_skill'])) {
            $this->toolbeltSkill = new Skill($this->getEnvironment(), $data['toolbelt_skill']);
        }
        if (isset($data['prev_chain'])) {
            $this->prevChain = new Skill($this->getEnvironment(), $data['prev_chain']);
        }
        if (isset($data['next_chain'])) {
            $this->nextChain = new Skill($this->getEnvironment(), $data['next_chain']);
        }
        if (isset($data['transform_skills']) && \is_array($data['transform_skills'])) {
            foreach ($data['transform_skills'] as $id) {
                $this->transformSkills[] = new Skill($this->getEnvironment(), $id);
            }
        }
        if (isset($data['bundle_skills']) && \is_array($data['bundle_skills'])) {
            foreach ($data['bundle_skills'] as $id) {
                $this->bundleSkills[] = new Skill($this->getEnvironment(), $id);
            }
        }
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function getApiName()
    {
        return 'skills';
    }
}
