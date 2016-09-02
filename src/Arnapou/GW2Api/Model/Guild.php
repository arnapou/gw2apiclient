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
 * @method string getTag()
 * @method array getEmblem()
 */
class Guild extends AbstractObject {

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->getData('guild_id');
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getData('guild_name');
    }

    /**
     * 
     * @return boolean
     */
    public function hasEmblem() {
        $emblem = $this->getEmblem();
        if (!is_array($emblem) || empty($emblem)) {
            return false;
        }
        return true;
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        $name = (string) $this->getName();
        $tag  = (string) $this->getTag();
        if ($name && $tag) {
            return $name . ' [' . $tag . ']';
        }
        else {
            return $name;
        }
    }

}
