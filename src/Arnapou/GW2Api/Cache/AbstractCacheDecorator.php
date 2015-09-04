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

abstract class AbstractCacheDecorator implements CacheInterface {

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

}
