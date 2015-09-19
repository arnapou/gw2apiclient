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
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Specialization extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $selectedTraits;

    /**
     *
     * @var array
     */
    protected $minorTraits;

    /**
     *
     * @var array
     */
    protected $majorTraits;

    /**
     * 
     * @param SimpleClient $client
     * @param integer $specializationId
     * @param array $selectedTraits
     */
    public function __construct(SimpleClient $client, $specializationId, $selectedTraits = []) {
        parent::__construct($client);

        $this->data           = $this->apiSpecializations($specializationId);
        $this->selectedTraits = $selectedTraits;
    }

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getSubkey(['name']);
    }

    /**
     * 
     * @return string
     */
    public function getProfession() {
        return $this->data['profession'];
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
        return $this->data['icon'];
    }

    /**
     * 
     * @return string
     */
    public function getBackground() {
        return $this->data['background'];
    }

    /**
     * 
     */
    protected function prepareTraits() {
        foreach ($this->data['minor_traits'] as $id) {
            self::$PRELOADS['traits'][] = $id;
        }
        foreach ($this->data['major_traits'] as $id) {
            self::$PRELOADS['traits'][] = $id;
        }

        $this->minorTraits = [];
        foreach ($this->data['minor_traits'] as $id) {
            $this->minorTraits[] = new SpecializationTrait($this->client, $id, in_array($id, $this->selectedTraits));
        }

        $this->majorTraits = [];
        foreach ($this->data['major_traits'] as $id) {
            $this->majorTraits[] = new SpecializationTrait($this->client, $id, in_array($id, $this->selectedTraits));
        }
    }

    /**
     * 
     * @return array
     */
    public function getMinorTraits() {
        if (!isset($this->minorTraits)) {
            $this->prepareTraits();
        }
        return $this->minorTraits;
    }

    /**
     * 
     * @return array
     */
    public function getMajorTraits() {
        if (!isset($this->majorTraits)) {
            $this->prepareTraits();
        }
        return $this->majorTraits;
    }

    /**
     * 
     * @return array
     */
    public function getMajorSelected() {
        $array = [null, null, null];
        foreach ($this->getMajorTraits() as /* @var $trait SpecializationTrait */ $trait) {
            if ($trait->isSelected()) {
                $array[$trait->getTier() - 1] = $trait;
            }
        }
        return $array;
    }

    /**
     * 
     * @return string
     */
    public function isElite() {
        return $this->data['elite'] ? true : false;
    }

}
