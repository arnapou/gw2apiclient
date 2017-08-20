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
 * @method string getType()
 * @method string getSlot()
 * @method string getAttunement() The Elementalist attunement that this skill requires. This field is usually only
 *         present for Elementalist skills.
 */
class ProfessionSkill extends AbstractObject
{

    /**
     *
     * @var Skill
     */
    protected $skill;

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
