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

use Arnapou\GW2Api\Core\AbstractClient;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Achievement extends AbstractObject {

    // TYPES
    const TYPE_DEFAULT                = 'Default';
    const TYPE_ITEMSET                = 'ItemSet';
    // FLAGS
    const FLAG_PVP                    = 'Pvp';
    const FLAG_CATEGORY_DISPLAY       = 'CategoryDisplay';
    const FLAG_MOVE_TO_TOP            = 'MoveToTop';
    const FLAG_IGNORE_NEARLY_COMPLETE = 'IgnoreNearlyComplete';

    /**
     * 
     * @param SimpleClient $client
     * @param array $id
     */
    public function __construct(SimpleClient $client, $id) {
        parent::__construct($client);

        $this->data = $this->apiAchievements($id);
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->data['id'];
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
    public function getName() {
        return $this->getSubkey(['name']);
    }

    /**
     * 
     * @return string
     */
    public function getDescription() {
        return strip_tags($this->getSubkey(['description']));
    }

    /**
     * 
     * @return string
     */
    public function getRequirement() {
        return $this->getSubkey(['requirement']);
    }

    /**
     * 
     * @return string
     */
    public function getFlags() {
        return $this->getSubkey(['flags']);
    }

    /**
     * 
     * @param string $flag
     * @return boolean
     */
    public function hasFlag($flag) {
        $flags = $this->getFlags();
        if (is_array($flags)) {
            return in_array($flag, $flags);
        }
        return false;
    }

}
