<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Storage;

interface StorageInterface
{
    /**
     *
     * @param string $lang
     * @param string $name
     * @param mixed  $id
     */
    public function prepare($lang, $name, $id);

    /**
     *
     * @param string   $lang
     * @param string   $name
     * @param mixed    $id
     * @param callable $fallback
     * @return array
     */
    public function get($lang, $name, $id, $fallback);

    /**
     *
     * @param string $lang
     * @param string $name
     * @param mixed  $id
     * @param array  $data
     */
    public function set($lang, $name, $id, $data);
}
