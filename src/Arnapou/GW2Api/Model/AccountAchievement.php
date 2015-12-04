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
class AccountAchievement extends AbstractObject {

    protected $achievement;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
        if (isset($data['id'])) {
            self::$PRELOADS['achievements'][] = $data['id'];
        }
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
    public function getCurrent() {
        return $this->getSubkey(['current']);
    }

    /**
     * 
     * @return string
     */
    public function getMax() {
        return $this->getSubkey(['max']);
    }

    /**
     * 
     * @return string
     */
    public function isDone() {
        return $this->data['id'] ? true : false;
    }

    /**
     * 
     * @return string
     */
    public function getBits() {
        return $this->getSubkey(['bits']);
    }

    /**
     * 
     * @return Achievement
     */
    public function getAchievement() {
        if (empty($this->achievement)) {
            $this->achievement = new Achievement($this->client, $this->data['id']);
        }
        return $this->achievement;
    }

}
