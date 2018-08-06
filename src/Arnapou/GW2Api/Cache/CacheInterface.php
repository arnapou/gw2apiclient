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

interface CacheInterface
{
    /**
     * Retrieve the value stored. Returns null if not found.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * Store a key/value pair.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expiration Number of seconds after when the data will expire.
     *                           If the value is equal to zero, the data will never expire.
     *                           If the value represents more than 30 days, the expiration is a timestamp.
     */
    public function set($key, $value, $expiration = 0);

    /**
     *
     * @param string $key
     * @return bool
     */
    public function exists($key);

    /**
     *
     * @param string $key
     */
    public function remove($key);
}
