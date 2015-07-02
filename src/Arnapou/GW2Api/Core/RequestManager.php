<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Core;

use Arnapou\GW2Api\Cache\CacheInterface;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Exception\JsonException;
use Arnapou\GW2Api\Exception\RequestException;

class RequestManager {

	/**
	 *
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 *
	 * @var integer
	 */
	protected $defautCacheRetention = 3600; // one hour

	/**
	 *
	 * @var int
	 */
	protected $curlRequestTimeout = 10;

	/**
	 *
	 * @var string
	 */
	protected $curlUserAgent = 'PHP Arnapou GW2 Api Client';

	/**
	 * 
	 */
	public function __construct() {
		
	}

	/**
	 * 
	 * @param Request $request
	 * @return array
	 */
	public function execute(Request $request, $cacheRetention = null) {
		$url = $request->getUrl();
		$parameters = $request->getParameters();
		$headers = $request->getHeaders();

		$requestUrl = $this->urlAppend($url, $parameters);
		
		if ($cacheRetention === null) {
			$cacheRetention = $this->defautCacheRetention;
		}
		if ($cacheRetention < 0) {
			$cacheRetention = 0;
		}

		// try to retrieve from cache
		$cacheKey = $requestUrl;
		if ($this->cache && $cacheRetention > 0) {
			$data = $this->cache->get($cacheKey);
			if ($data !== null) {
				return $data;
			}
		}

		$curl = new Curl();
		$curl->setUrl($requestUrl);
		$curl->setUserAgent($this->curlUserAgent);
		$curl->setTimeout($this->curlRequestTimeout);
		$curl->setHeaders($headers);
		$curl->setGet();
		$response = new CurlResponse($curl);
		if ($response->getErrorCode()) {
			throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
		}
		$data = $this->jsonDecode($response->getContent());

		// store in cache if needed
		if ($this->cache && $cacheRetention > 0) {
			$this->cache->set($cacheKey, $data, $cacheRetention);
		}
		return $data;
	}

	/**
	 * 
	 * @param string $url
	 * @param string|array $params
	 * @return string
	 */
	protected function urlAppend($url, $params) {
		if (empty($params)) {
			return $url;
		}
		$url .= (strpos($url, '?') === false) ? '?' : '&';
		if (is_array($params)) {
			$url .= http_build_query($params);
		}
		else {
			$url .= (string) $params;
		}
		return $url;
	}

	/**
	 * 
	 * @param string $json
	 * @return array
	 */
	protected function jsonDecode($json) {
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
			throw new JsonException('Json error : ' . (isset($errors[$jsonLastError]) ? $errors[$jsonLastError] : 'Unknown error'));
		}
		return $array;
	}

	/**
	 * 
	 * @return integer
	 */
	public function getDefautCacheRetention() {
		return $this->defautCacheRetention;
	}

	/**
	 * 
	 * @param integer $retention
	 */
	public function setDefautCacheRetention($retention) {
		if ($retention <= 1) {
			throw new Exception('Retention cannot be lower than 2 seconds');
		}
		$this->defautCacheRetention = $retention;
	}

	/**
	 * 
	 * @return CacheInterface|null
	 */
	public function getCache() {
		return $this->cache;
	}

	/**
	 * 
	 * @param CacheInterface $cache
	 */
	public function setCache(CacheInterface $cache) {
		$this->cache = $cache;
	}

	/**
	 * 
	 * @return string
	 */
	public function getCurlUserAgent() {
		return $this->curlUserAgent;
	}

	/**
	 * 
	 * @param string $userAgent
	 */
	public function setCurlUserAgent($userAgent) {
		$this->curlUserAgent = $userAgent;
	}

	/**
	 * 
	 * @return int
	 */
	public function getCurlRequestTimeout() {
		return $this->curlRequestTimeout;
	}

	/**
	 * 
	 * @param int $seconds
	 */
	public function setCurlRequestTimeout($seconds) {
		$this->curlRequestTimeout = $seconds;
	}

}
