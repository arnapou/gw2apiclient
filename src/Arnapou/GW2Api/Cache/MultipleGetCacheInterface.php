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

interface MultipleGetCacheInterface {

    /**
     * Retrieve the values stored. Returns array empty if none found.
     * 
     * @param array $keys
     * @param string $prefix
     * @return array
     */
    public function getMultiple($keys, $prefix = '');
}
