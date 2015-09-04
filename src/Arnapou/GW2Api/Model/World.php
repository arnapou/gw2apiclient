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
class World extends AbstractObject {

    // REGIONS
    const REGION_NA = 'NA';
    const REGION_EU = 'EU';

    /**
     * 
     * @param SimpleClient $client
     * @param integer $id
     */
    public function __construct(SimpleClient $client, $id) {
        parent::__construct($client);

        $data = $this->client->v2_worlds($id);
        if (!is_array($data) || !isset($data[$id])) {
            throw new Exception('Invalid received world data.');
        }
        $this->data = $data[$id];
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
    public function getName() {
        return $this->data['name'];
    }

    /**
     * 
     * @return string
     */
    public function getPopulation() {
        return $this->data['population'];
    }

    /**
     * 
     * @return string EU or NA
     */
    public function getRegion() {
        $int = substr($this->data['id'], 0, 1);
        if ($int == '1') {
            return self::REGION_NA;
        }
        elseif ($int == '2') {
            return self::REGION_EU;
        }
        throw new Exception('Unkown region');
    }

}
