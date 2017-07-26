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
 * @method string getName()
 * @method string getIcon()
 * @method string getIconBig()
 */
class Profession extends AbstractStoredObject
{

    protected $specializations = [];
    protected $weapons         = [];
    protected $training        = [];
    protected $skills          = [];

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();

        if (isset($data['specializations']) && is_array($data['specializations'])) {
            foreach ($data['specializations'] as $id) {
                $this->specializations[] = new Specialization($env, $id);
            }
        }

        if (isset($data['weapons']) && is_array($data['weapons'])) {
            foreach ($data['weapons'] as $type => $item) {
                $item['type']    = $type;
                $this->weapons[] = new ProfessionWeapon($env, $item);
            }
        }

        if (isset($data['training']) && is_array($data['training'])) {
            foreach ($data['training'] as $item) {
                $obj                           = new ProfessionTraining($env, $item);
                $this->training[$obj->getId()] = $obj;
            }
        }

        if (isset($data['skills']) && is_array($data['skills'])) {
            foreach ($data['skills'] as $item) {
                $obj                         = new ProfessionSkill($env, $item);
                $this->skills[$obj->getId()] = $obj;
            }
        }
    }

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
    public function getWeapons()
    {
        $this->checkLoadData();
        return $this->weapons;
    }

    /**
     *
     * @return array
     */
    public function getTraining()
    {
        $this->checkLoadData();
        return $this->training;
    }

    /**
     *
     * @return array
     */
    public function getSpecializations()
    {
        $this->checkLoadData();
        return $this->specializations;
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
        return 'professions';
    }
}
