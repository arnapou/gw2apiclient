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
 */
class DungeonPath extends AbstractObject {

    use UnlockTrait;

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->getData('id');
    }

    /**
     * 
     * @return integer
     */
    public function getNumber() {
        return $this->getData('number');
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return \Arnapou\GW2Api\id_to_name($this->getId());
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

}
