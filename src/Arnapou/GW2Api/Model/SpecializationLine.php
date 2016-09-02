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

/**
 *
 * @method getName()
 * @method getIcon()
 * @method getBackground()
 * @method getProfession()
 * @method getMinorTraitsIds()
 * @method getMajorTraitsIds()
 * @method getMinorTraits()
 * @method getMajorTraits()
 */
class SpecializationLine extends AbstractObject {

    /**
     *
     * @var Specialization
     */
    protected $specialization = null;

    /**
     *
     * @var array
     */
    protected $traits = [];

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['id'])) {
            $this->specialization = new Specialization($this->getEnvironment(), $data['id']);
        }
        if (isset($data['traits']) && is_array($data['traits'])) {
            foreach ($data['traits'] as $id) {
                $this->traits[] = new SpecializationTrait($this->getEnvironment(), $id);
            }
        }
    }

    public function __call($name, $arguments) {
        $val = parent::__call($name, $arguments);
        if ($val === null && empty($arguments) && $this->specialization) {
            return $this->specialization->$name();
        }
        return $val;
    }

    /**
     * 
     * @return boolean
     */
    public function isElite() {
        return $this->specialization && $this->specialization->isElite() ? true : false;
    }

    /**
     * 
     * @return array
     */
    public function getTraitsIds() {
        return $this->getData('traits');
    }

    /**
     * 
     * @return array
     */
    public function getTraits() {
        return $this->traits;
    }

    /**
     * 
     * @return integer
     */
    public function getSpecializationId() {
        return $this->getData('id');
    }

    /**
     * 
     * @return Specialization
     */
    public function getSpecialization() {
        return $this->specialization;
    }

}
