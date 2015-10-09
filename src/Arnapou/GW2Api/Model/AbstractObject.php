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
    protected static $PRELOADS = [
        'colors' => [],
        'items'  => [],
        'prices' => [],
        'skins'  => [],
    ];

    /**
     * 
     * @param SimpleClient $client
     */
    public function __construct(SimpleClient $client) {
        $this->client = $client;
    }

    /**
     * 
     * @return SimpleClient
     */
    public function getClient() {
        return $this->client;
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
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiMaps($ids) {
        if (!empty(self::$PRELOADS['maps'])) {
            $this->client->v2_maps(self::$PRELOADS['maps']);
            self::$PRELOADS['maps'] = [];
        }
        $result = $this->client->v2_maps($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiAchievements($ids) {
        if (!empty(self::$PRELOADS['achievements'])) {
            $this->client->v2_achievements(self::$PRELOADS['achievements']);
            self::$PRELOADS['achievements'] = [];
        }
        $result = $this->client->v2_achievements($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiItems($ids) {
        if (!empty(self::$PRELOADS['items'])) {
            $this->client->v2_items(self::$PRELOADS['items']);
            self::$PRELOADS['items'] = [];
        }
        $result = $this->client->v2_items($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiColors($ids) {
        if (!empty(self::$PRELOADS['colors'])) {
            $this->client->v2_items(self::$PRELOADS['colors']);
            self::$PRELOADS['colors'] = [];
        }
        $result = $this->client->v2_colors($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiSkins($ids) {
        if (!empty(self::$PRELOADS['skins'])) {
            $this->client->v2_skins(self::$PRELOADS['skins']);
            self::$PRELOADS['skins'] = [];
        }
        $result = $this->client->v2_skins($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiTraits($ids) {
        if (!empty(self::$PRELOADS['traits'])) {
            $this->client->v2_traits(self::$PRELOADS['traits']);
            self::$PRELOADS['traits'] = [];
        }
        $result = $this->client->v2_traits($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiSpecializations($ids) {
        if (!empty(self::$PRELOADS['specializations'])) {
            $this->client->v2_specializations(self::$PRELOADS['specializations']);
            self::$PRELOADS['specializations'] = [];
        }
        $result = $this->client->v2_specializations($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    protected function apiPrices($ids) {
        if (!empty(self::$PRELOADS['prices'])) {
            $this->client->v2_commerce_prices(self::$PRELOADS['prices']);
            self::$PRELOADS['prices'] = [];
        }
        $result = $this->client->v2_commerce_prices($ids);
        return (!is_array($ids) && isset($result[$ids])) ? $result[$ids] : $result;
    }

    /**
     * 
     * @param array $slots
     */
    protected function preloadSlots($slots) {
        foreach ($slots as $equipment) {
            if (empty($equipment) || !isset($equipment['id'])) {
                continue;
            }
            self::$PRELOADS['items'][]  = $equipment['id'];
            self::$PRELOADS['prices'][] = $equipment['id'];
            if (isset($equipment['infusions'])) {
                foreach ($equipment['infusions'] as $id) {
                    self::$PRELOADS['items'][]  = $id;
                    self::$PRELOADS['prices'][] = $id;
                }
            }
            if (isset($equipment['upgrades'])) {
                foreach ($equipment['upgrades'] as $id) {
                    self::$PRELOADS['items'][]  = $id;
                    self::$PRELOADS['prices'][] = $id;
                }
            }
            if (isset($equipment['skin'])) {
                self::$PRELOADS['skins'][] = $equipment['skin'];
            }
        }
    }

    /**
     * 
     * @param array $ids
     */
    protected function preloadItemIds($ids) {
        foreach ($ids as $id) {
            self::$PRELOADS['items'][]  = $id;
            self::$PRELOADS['prices'][] = $id;
        }
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
