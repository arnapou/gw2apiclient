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
abstract class AbstractObject {

    /**
     *
     * @var SimpleClient
     */
    protected $client;

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var array
     */
    private $stackItemIds = [];

    /**
     *
     * @var array
     */
    private $stackSkinIds = [];

    /**
     * 
     * @param SimpleClient $client
     */
    public function __construct(SimpleClient $client) {
        $this->client = $client;
    }

    /**
     * 
     * @param array $slots
     */
    protected function prepareSlots($slots) {
        foreach ($slots as $equipment) {
            if (empty($equipment) || !isset($equipment['id'])) {
                continue;
            }
            $this->stackItemIds[] = $equipment['id'];
            if (isset($equipment['infusions'])) {
                foreach ($equipment['infusions'] as $id) {
                    $this->stackItemIds[] = $id;
                }
            }
            if (isset($equipment['upgrades'])) {
                foreach ($equipment['upgrades'] as $id) {
                    $this->stackItemIds[] = $id;
                }
            }
            if (isset($equipment['skin'])) {
                $this->stackSkinIds[] = $equipment['skin'];
            }
        }
    }

    /**
     * 
     * @param array $ids
     */
    protected function prepareItemIds($ids) {
        foreach ($ids as $id) {
            $this->stackItemIds[] = $id;
        }
    }

    /**
     * 
     */
    protected function prepareFlush() {
        if (!empty($this->stackItemIds)) {
            $this->apiItems($this->stackItemIds);
            $this->apiPrices($this->stackItemIds);
        }
        if (!empty($this->stackSkinIds)) {
            $this->apiSkins($this->stackSkinIds);
        }
        $this->stackItemIds = [];
        $this->stackSkinIds = [];
    }

    /**
     * Function which retrieve a subkey without any error
     * 
     * @param array $keys
     * @param mixed $default
     * @return mixed
     */
    protected function getSubkey($keys, $default = null) {
        $item = $this->data;
        foreach ($keys as $key) {
            if (!isset($item[$key])) {
                return $default;
            }
            $item = $item[$key];
        }
        return $item;
    }

    /**
     * 
     * @param mixed $id
     * @return string
     */
    protected function apiIcon($id) {
        try {
            $file = $this->client->getClientV2()->apiFiles($id)->execute(7 * 86400)->getData();
            if (is_array($file) && isset($file[0]) && isset($file[0]['icon'])) {
                return $file[0]['icon'];
            }
        }
        catch (\Exception $e) {
            
        }
        return null;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiCharacters($ids) {
        return $this->client->getClientV2()->smartRequest('apiCharacters', $ids, 7 * 86400, 'name');
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiItems($ids) {
        return $this->client->getClientV2()->smartRequest('apiItems', $ids, 7 * 86400);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiSkins($ids) {
        return $this->client->getClientV2()->smartRequest('apiSkins', $ids, 7 * 86400);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiPrices($ids) {
        return $this->client->getClientV2()->smartRequest('apiCommercePrices', $ids, 1800);
    }

    /**
     * 
     * @return array
     */
    public function getRawData() {
        return $this->data;
    }

}