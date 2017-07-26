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
 * @method string getCost()
 * @method string getType()
 * @method string getSkillId()
 * @method string getTraitId()
 */
class ProfessionTrainingTrackElement extends AbstractObject
{

    const TYPE_TRAIT = 'Trait';
    const TYPE_SKILL = 'Skill';

    /**
     *
     * @var Skill
     */
    protected $skill;

    /**
     *
     * @var SpecializationTrait
     */
    protected $trait;

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();

        if (isset($data['skill_id'])) {
            $this->skill = new Skill($env, $data['skill_id']);
        }

        if (isset($data['trait_id'])) {
            $this->trait = new SpecializationTrait($env, $data['trait_id']);
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

    /**
     *
     * @return SpecializationTrait
     */
    public function getTrait()
    {
        return $this->trait;
    }
}
