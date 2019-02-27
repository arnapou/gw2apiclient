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

class MemoryCache implements CacheInterface
{
    /**
     *
     * @var array
     */
    protected $memory = [];

    public function exists($key)
    {
        if (\array_key_exists($key, $this->memory)) {
            return true;
        }
        return $this->cache->exists($key);
    }

    public function get($key)
    {
        if (\array_key_exists($key, $this->memory)) {
            return $this->memory[$key];
        }
        return null;
    }

    public function remove($key)
    {
        unset($this->memory[$key]);
    }

    public function set($key, $value, $expiration = 0)
    {
        $this->memory[$key] = $value;
    }
}
