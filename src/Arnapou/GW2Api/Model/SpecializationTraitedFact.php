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
class SpecializationTraitedFact extends SpecializationTraitFact {

    /**
     *
     * @var SpecializationTrait
     */
    protected $requires_trait;

    /**
     * 
     * @return string
     */
    public function getRequireTrait() {
        if (!isset($this->requires_trait)) {
            $this->requires_trait = new SpecializationTrait($this->client, $this->data['requires_trait']);
        }
        return $this->requires_trait;
    }

    /**
     * 
     * @return integer
     */
    public function getOverrides() {
        return $this->getSubkey(['overrides']);
    }

}
