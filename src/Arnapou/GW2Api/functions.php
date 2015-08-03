<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api;

/**
 * 
 * @param array $array
 * @return boolean
 */
function is_associative_array($array) {
	$values = array_values($array);
	$diff = array_diff_key($values, $array);
	return empty($diff) ? false : true;
}
