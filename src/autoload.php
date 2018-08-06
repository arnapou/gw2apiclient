<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include_once __DIR__ . '/Arnapou/GW2Api/functions.php';

spl_autoload_register(function ($class) {
    if (0 == strpos($class, 'Arnapou\GW2Api')) {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (is_file($filename)) {
            include $filename;
        }
    }
});
