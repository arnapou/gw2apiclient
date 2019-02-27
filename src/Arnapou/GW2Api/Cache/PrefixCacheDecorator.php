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

class PrefixCacheDecorator implements CacheInterface
{
    /**
     *
     * @var string
     */
    protected $prefix;

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     *
     * @param string         $prefix
     * @param CacheInterface $cache
     */
    public function __construct($prefix, CacheInterface $cache)
    {
        $this->cache  = $cache;
        $this->prefix = $prefix;
    }

    public function exists($key)
    {
        return $this->cache->exists($this->prefix . $key);
    }

    public function get($key)
    {
        return $this->cache->get($this->prefix . $key);
    }

    public function remove($key)
    {
        $this->cache->remove($this->prefix . $key);
    }

    public function set($key, $value, $expiration = 0)
    {
        $this->cache->set($this->prefix . $key, $value, $expiration);
    }
}
