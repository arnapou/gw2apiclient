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
	 * @return CacheInterface
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
