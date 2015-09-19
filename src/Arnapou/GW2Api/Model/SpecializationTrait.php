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
class SpecializationTrait extends AbstractObject {

    const SLOT_MAJOR = 'Major';
    const SLOT_MINOR = 'Minor';

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var array
     */
    protected $facts;

    /**
     *
     * @var array
     */
    protected $traitedfacts;

    /**
     *
     * @var array
     */
    protected $skills;

    /**
     *
     * @var Specialization
     */
    protected $specialization;

    /**
     *
     * @var boolean
     */
    protected $selected;

    /**
     * 
     * @param SimpleClient $client
     * @param integer $traitId
     * @param boolean $selected
     */
    public function __construct(SimpleClient $client, $traitId, $selected = false) {
        parent::__construct($client);

        $this->data     = $this->apiTraits($traitId);
        $this->selected = $selected;
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
     * @return boolean
     */
    public function isSelected() {
        return $this->selected ? true : false;
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
        return $this->getSubkey(['icon']);
    }

    /**
     * 
     * @return string
     */
    public function getDescription() {
        return $this->data['description'];
    }

    /**
     * 
     * @return Specialization
     */
    public function getSpecialization() {
        if (!isset($this->specialization)) {
            $this->specialization = new Specialization($this->client, $this->data['specialization']);
        }
        return $this->specialization;
    }

    /**
     * 
     * @return integer
     */
    public function getTier() {
        return $this->data['description'];
    }

    /**
     * 
     * @return string
     */
    public function getSkills() {
        if (!isset($this->skills)) {
            $this->skills = [];
            if (isset($this->data['skills'])) {
                foreach ($this->data['skills'] as $skill) {
                    $this->skills[] = new SpecializationTraitSkill($this->client, $skill);
                }
            }
        }
        return $this->skills;
    }

    /**
     * 
     * @return string
     */
    public function getFacts() {
        if (!isset($this->facts)) {
            $this->facts = [];
            if (isset($this->data['facts'])) {
                foreach ($this->data['facts'] as $fact) {
                    $this->facts[] = new SpecializationTraitFact($this->client, $fact);
                }
            }
        }
        return $this->facts;
    }

    /**
     * 
     * @return string
     */
    public function getTraitedFacts() {
        if (!isset($this->traitedfacts)) {
            $this->traitedfacts = [];
            if (isset($this->data['traited_facts'])) {
                foreach ($this->data['traited_facts'] as $fact) {
                    $this->traitedfacts[] = new SpecializationTraitedFact($this->client, $fact);
                }
            }
        }
        return $this->traitedfacts;
    }

    /**
     * 
     * @return string
     */
    public function getSlot() {
        return $this->data['slot'];
    }

}
