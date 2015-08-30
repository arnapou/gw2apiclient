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
class Equipment extends InventorySlot {

    /**
     *
     * @var string
     */
    protected $slot;


    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client, $data);

        $this->equipment = $data;
        $this->slot      = $data['slot'];
    }

    /**
     * 
     * @return string
     */
    public function getSlot() {
        return $this->slot;
    }


}
