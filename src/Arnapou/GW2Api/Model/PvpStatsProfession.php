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
class PvpStatsProfession extends PvpStats {

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
    public function getProfessionIcon() {
        return $this->apiIcon('icon_' . strtolower($this->getProfession()));
    }

    /**
     * 
     * @return string
     */
    public function getProfessionIconBig() {
        return $this->apiIcon('icon_' . strtolower($this->getProfession()) . '_big');
    }

}
