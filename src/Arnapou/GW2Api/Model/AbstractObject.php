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
     * @param mixed $id
     * @return string
     */
    protected function apiIcon($id) {
        try {
            $file = $this->client->v2_files($id);
            if (is_array($file) && isset($file[$id]) && isset($file[$id]['icon'])) {
                return $file[$id]['icon'];
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
        $result = $this->client->v2_characters($ids);
        return is_array($ids) ? $result : (isset($result[$ids]) ? $result[$ids] : $result);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiItems($ids) {
        $result = $this->client->v2_items($ids);
        return is_array($ids) ? $result : (isset($result[$ids]) ? $result[$ids] : $result);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiColors($ids) {
        $result = $this->client->v2_colors($ids);
        return is_array($ids) ? $result : (isset($result[$ids]) ? $result[$ids] : $result);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiSkins($ids) {
        $result = $this->client->v2_skins($ids);
        return is_array($ids) ? $result : (isset($result[$ids]) ? $result[$ids] : $result);
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiPrices($ids) {
        $result = $this->client->v2_commerce_prices($ids);
        return is_array($ids) ? $result : (isset($result[$ids]) ? $result[$ids] : $result);
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
     * @return array
     */
    public function getRawData() {
        return $this->data;
    }

}
