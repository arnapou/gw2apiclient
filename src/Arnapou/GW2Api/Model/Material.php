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
 * @method string getName()
 * @method string getOrder()
 */
class Material extends AbstractStoredObject {

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    protected function getApiName() {
        return 'materials';
    }

    /**
     * 
     * @return array
     */
    public function getItemIds() {
        return $this->getData('items', []);
    }

}
