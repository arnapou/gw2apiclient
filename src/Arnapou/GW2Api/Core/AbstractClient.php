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
use Arnapou\GW2Api\Exception\RequestException;

abstract class AbstractClient {

	const LANG_DE = 'de';
	const LANG_EN = 'en';
	const LANG_ES = 'es';
	const LANG_FR = 'fr';

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
	 * @var CurlResponse
	 */
	protected $lastResponse = null;

	/**
	 *
	 * @var string
	 */
	protected $lastRequestUrl = null;

	/**
	 *
	 * @var string
	 */
	protected $lang = null;

	/**
	 *
	 * @var CacheInterface
	 */
	protected $cache = null;

	/**
	 * 
	 */
	public function __construct() {
		
	}

	/**
	 * 
	 * @return string base url with ending slash
	 */
	abstract public function getBaseUrl();

	/**
	 * 
	 * @param string $url
	 * @param array $parameters
	 * @param array $headers
	 * @param array $cacheRetention retention: 5 min by default
	 * @return array
	 */
	protected function request($url, $parameters = [], $headers = [], $cacheRetention = 300) {
		if (!isset($parameters['lang']) && !empty($this->lang)) {
			$parameters['lang'] = $this->lang;
		}
		$requestUrl = \Arnapou\GW2Api\url_append($this->getBaseUrl() . $url, $parameters);
		$this->lastRequestUrl = $requestUrl;

		// try to retrieve from cache
		$cacheKey = $requestUrl;
		if ($this->cache) {
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
		$this->lastResponse = $response;
		if ($response->getErrorCode()) {
			throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
		}
		$data = \Arnapou\GW2Api\json_decode($response->getContent());

		// store in cache if needed
		if ($this->cache) {
			$this->cache->set($cacheKey, $data, $cacheRetention <= 0 ? 5 : $cacheRetention);
		}
		return $data;
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
	 * @return CurlResponse|null
	 */
	public function getLastResponse() {
		return $this->lastResponse;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getLastRequestUrl() {
		return $this->lastRequestUrl;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * 
	 * @param string $lang
	 */
	public function setLang($lang) {
		$this->lang = $lang;
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
	 * @return string
	 */
	public function getCurlUserAgent() {
		return $this->curlUserAgent;
	}

	/**
	 * 
	 * @param int $seconds
	 */
	public function setCurlRequestTimeout($seconds) {
		$this->curlRequestTimeout = $seconds;
	}

	/**
	 * 
	 * @param string $userAgent
	 */
	public function setCurlUserAgent($userAgent) {
		$this->curlUserAgent = $userAgent;
	}

}
