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
class SpecializationTraitSkill extends AbstractObject {

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
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
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
        return $this->data['name'];
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
     * @return string
     */
    public function getIcon() {
        return $this->data['icon'];
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

}
