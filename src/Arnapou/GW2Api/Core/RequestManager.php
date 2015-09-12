<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Core;

use Arnapou\GW2Api\Cache\CacheInterface;
use Arnapou\GW2Api\Event\EventListener;
use Arnapou\GW2Api\Exception\Exception;

class RequestManager {

    const onRequest = 'onRequest';

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     *
     * @var array
     */
    protected $cacheUrlPolicy = [
        '/v1/guild_details'     => 86400, // one day
        '/v2/colors'            => 604000, // one week
        '/v2/commerce/listings' => 1800, // 30 min
        '/v2/commerce/prices'   => 1800, // 30 min
        '/v2/characters'        => 1800, // 30 min
        '/v2/currencies'        => 86400, // one day
        '/v2/files'             => 604000, // one week
        '/v2/items'             => 604000, // one week
        '/v2/maps'              => 604000, // one week
        '/v2/materials'         => 604000, // one week
        '/v2/quaggans'          => 604000, // one week
        '/v2/recipes'           => 604000, // one week
        '/v2/skins'             => 604000, // one week
        '/v2/specializations'   => 604000, // one week
        '/v2/traits'            => 604000, // one week
        '/v2/worlds'            => 604000, // one week
    ];

    /**
     *
     * @var integer
     */
    protected $defautCacheRetention = 3600; // one hour

    /**
     *
     * @var int
     */
    protected $curlRequestTimeout = 10;

    /**
     *
     * @var string
     */
    protected $curlUserAgent = 'PHP Arnapou GW2 Api Client';

    /**
     *
     * @var EventListener
     */
    protected $eventListener;

    /**
     * 
     */
    public function __construct() {
        $this->eventListener = new EventListener();
    }

    /**
     * 
     * @return EventListener
     */
    public function getEventListener() {
        return $this->eventListener;
    }

    /**
     * 
     * @param string $url
     * @param integer $retention
     * @return RequestManager
     */
    public function addCacheRetentionPolicy($url, $retention) {
        $this->cacheUrlPolicy[$url] = $retention;
        return $this;
    }

    /**
     * 
     * @return RequestManager
     */
    public function clearCacheRetentionPolicy() {
        $this->cacheUrlPolicy = [];
        return $this;
    }

    /**
     * 
     * @param string $url
     * @return integer
     */
    public function getCacheRetention($url) {
        foreach ($this->cacheUrlPolicy as $string => $retention) {
            if (strpos($url, $string) !== false) {
                return $retention;
            }
        }
        return $this->defautCacheRetention;
    }

    /**
     * 
     * @return integer
     */
    public function getDefautCacheRetention() {
        return $this->defautCacheRetention;
    }

    /**
     * 
     * @param integer $retention
     */
    public function setDefautCacheRetention($retention) {
        if ($retention <= 1) {
            throw new Exception('Retention cannot be lower than 2 seconds');
        }
        $this->defautCacheRetention = $retention;
    }

    /**
     * 
     * @return boolean
     */
    public function hasCache() {
        return $this->cache ? true : false;
    }

    /**
     * 
     * @return CacheInterface
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * 
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache) {
        $this->cache = $cache;
    }

    /**
     * 
     * @return string
     */
    public function getCurlUserAgent() {
        return $this->curlUserAgent;
    }

    /**
     * 
     * @param string $userAgent
     */
    public function setCurlUserAgent($userAgent) {
        $this->curlUserAgent = $userAgent;
    }

    /**
     * 
     * @return int
     */
    public function getCurlRequestTimeout() {
        return $this->curlRequestTimeout;
    }

    /**
     * 
     * @param int $seconds
     */
    public function setCurlRequestTimeout($seconds) {
        $this->curlRequestTimeout = $seconds;
    }

}
