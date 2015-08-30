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
class Currency extends AbstractObject {

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
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
    public function getDescription() {
        return $this->data['description'];
    }

    /**
     * 
     * @return string
     */
    public function getOrder() {
        return $this->data['order'];
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
        return $this->data['icon'];
    }

    /**
     * 
     * @return string
     */
    public function getQuantity() {
        return $this->data['quantity'];
    }

}
