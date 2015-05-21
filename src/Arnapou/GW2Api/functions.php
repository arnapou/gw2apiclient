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

use Arnapou\GW2Api\Exception\Exception;

/**
 * 
 * @param string $json
 * @return array
 */
function json_decode($json) {
	$json = trim($json);
	if ($json === '' || ($json[0] !== '{' && $json[0] !== '[' && $json[0] !== '"')) {
		throw new Exception('Json not valid');
	}
	$array = \json_decode($json, true);
	$jsonLastError = json_last_error();
	if ($jsonLastError !== JSON_ERROR_NONE) {
		$errors = array(
			JSON_ERROR_DEPTH => 'Max depth reached.',
			JSON_ERROR_STATE_MISMATCH => 'Mismatch modes or underflow.',
			JSON_ERROR_CTRL_CHAR => 'Character control error.',
			JSON_ERROR_SYNTAX => 'Malformed JSON.',
			JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, probably charset problem.',
			JSON_ERROR_RECURSION => 'Recursion detected.',
			JSON_ERROR_INF_OR_NAN => 'Inf or NaN',
			JSON_ERROR_UNSUPPORTED_TYPE => 'Unsupported type.',
		);
		throw new Exception('Json error : ' . (isset($errors[$jsonLastError]) ? $errors[$jsonLastError] : 'Unknown error'));
	}
	return $array;
}
