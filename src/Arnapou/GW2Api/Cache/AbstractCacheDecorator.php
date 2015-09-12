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
     * @param string $prefix
     * @return array
     */
    public function getMultiple($keys, $prefix = '') {
        if ($this->cache instanceof MultipleGetCacheInterface) {
            return $this->cache->getMultiple($keys, $prefix);
        }
        else {
            $return = [];
            foreach ($keys as $key) {
                $value = $this->cache->get($prefix . $key);
                if ($value !== null) {
                    $return[$prefix . $key] = $value;
                }
            }
            return $return;
        }
    }

}
