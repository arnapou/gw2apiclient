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

class Request {

	/**
	 *
	 * @var RequestManager
	 */
	protected $manager;

	/**
	 *
	 * @var integer
	 */
	protected $url;

	/**
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 *
	 * @var array
	 */
	protected $headers;

	/**
	 * 
	 */
	public function __construct(RequestManager $manager, $url, $parameters = [], $headers = []) {
		$this->manager = $manager;
		$this->url = $url;
		$this->parameters = $parameters;
		$this->headers = $headers;
	}

	/**
	 * 
	 * @return array
	 */
	public function execute($cacheRetention = null) {
		return $this->manager->execute($this, $cacheRetention);
	}

	/**
	 * 
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * 
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * 
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

}
