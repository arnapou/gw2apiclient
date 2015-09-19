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
class Map extends AbstractObject {

    /**
     * 
     * @param SimpleClient $client
     * @param integer $mapid
     */
    public function __construct(SimpleClient $client, $mapid) {
        parent::__construct($client);

        $this->data = $this->apiMaps($mapid);
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
    public function getMinLevel() {
        return $this->data['min_level'];
    }

    /**
     * 
     * @return string
     */
    public function getMaxLevel() {
        return $this->data['max_level'];
    }

    /**
     * 
     * @return string
     */
    public function getDefaultFloor() {
        return $this->data['default_floor'];
    }

    /**
     * 
     * @return string
     */
    public function getFloors() {
        return $this->data['floors'];
    }

    /**
     * 
     * @return string
     */
    public function getRegionId() {
        return $this->data['region_id'];
    }

    /**
     * 
     * @return string
     */
    public function getRegionName() {
        return $this->data['region_name'];
    }

    /**
     * 
     * @return string
     */
    public function getContinentId() {
        return $this->data['continent_id'];
    }

    /**
     * 
     * @return string
     */
    public function getContinentName() {
        return $this->data['continent_name'];
    }

    /**
     * 
     * @return string
     */
    public function getMapRect() {
        return $this->data['map_rect'];
    }

    /**
     * 
     * @return string
     */
    public function getContinentRect() {
        return $this->data['continent_rect'];
    }

}
