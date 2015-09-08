<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Cache;

abstract class AbstractCacheDecorator implements CacheInterface, MultipleGetCacheInterface {

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * 
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
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
     * @param array $keys
     * @return array
     */
    public function getMultiple($keys) {
        if ($this->cache instanceof MultipleGetCacheInterface) {
            return $this->cache->getMultiple($keys);
        }
        else {
            $return = [];
            foreach ($keys as $key) {
                $value = $this->cache->get($key);
                if ($value !== null) {
                    $return[$key] = $value;
                }
            }
            return $return;
        }
    }

}
