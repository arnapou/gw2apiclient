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

use Arnapou\GW2Api\Exception\Exception;

class MemcachedCache implements CacheInterface {

	protected $memcached;

	public function __construct($server = 'localhost', $port = 11211) {
		if (!extension_loaded('memcached')) {
			throw new Exception("The memcached PHP extension is not loaded.");
		}
		$this->memcached = new \Memcached();
		$this->memcached->addServer($server, $port);
	}

	public function exists($key) {
		$value = $this->memcached->get($key);
		if ($this->memcached->getResultCode() == \Memcached::RES_NOTFOUND) {
			return false;
		}
		return true;
	}

	public function get($key) {
		$value = $this->memcached->get($key);
		if ($this->memcached->getResultCode() == \Memcached::RES_NOTFOUND) {
			return null;
		}
		return $value;
	}

	public function remove($key) {
		$this->memcached->delete($key);
	}

	public function set($key, $value, $expiration = 0) {
		$this->memcached->set($key, $value, $expiration);
	}

}
