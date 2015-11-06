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
class Mini extends AbstractObject {

    /**
     *
     * @var boolean
     */
    protected $unlocked;

    /**
     * 
     * @param SimpleClient $client
     * @param array $id
     * @param boolean $unlocked
     */
    public function __construct(SimpleClient $client, $id, $unlocked = false) {
        parent::__construct($client);

        $this->data     = $this->apiMinis($id);
        $this->unlocked = $unlocked;
    }

    /**
     * 
     * @return boolean
     */
    public function isUnlocked() {
        return $this->unlocked;
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
        return $this->getSubkey(['name']);
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
     * @return integer
     */
    public function getOrder() {
        return $this->getSubkey(['order'], 0);
    }

    /**
     * 
     * @return string
     */
    public function getUnlock() {
        return $this->getSubkey(['unlock']);
    }

    /**
     * 
     * @return integer
     */
    public function getItemId() {
        return $this->getSubkey(['item_id']);
    }

    /**
     * 
     * @return Item
     */
    public function getItem() {
        $id = $this->getItemId();
        if ($id) {
            return new Item($this->client, $id);
        }
        return null;
    }

}
