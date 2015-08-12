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

class RequestBatch implements RequestInterface {

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
	protected $parameterSets = [];

	/**
	 *
	 * @var array
	 */
	protected $headers;

	/**
	 * 
	 */
	public function __construct(RequestManager $manager, $url, $headers = []) {
		$this->manager = $manager;
		$this->url = $url;
		$this->headers = $headers;
	}

	/**
	 * 
	 * @param array $parameters
	 * @return RequestBatch
	 */
	public function addParameterSet($parameters) {
		$this->parameterSets[] = $parameters;
		return $this;
	}

	/**
	 * 
	 * @param string $param
	 * @return integer
	 */
	public function getParameter($param) {
		$n = count($this->parameterSets);
		for ($i = 0; $i < $n; $i++) {
			// return only the first found
			if (isset($this->parameters[$param])) {
				return $this->parameters[$param];
			}
		}
		return null;
	}

	/**
	 * 
	 * @param string $param
	 * @param string $value
	 * @return Request
	 */
	public function setParameter($param, $value) {
		$n = count($this->parameterSets);
		for ($i = 0; $i < $n; $i++) {
			$this->parameterSets[$i][$param] = $value;
		}
		return $this;
	}

	/**
	 * 
	 * @return Response
	 */
	public function execute($cacheRetention = null) {
		$result = [];
		$n = count($this->parameterSets);
		for ($i = 0; $i < $n; $i++) {
			$parameters = $this->parameterSets[$i];
			$request = new Request($this->manager, $this->url, $parameters, $this->headers);
			$response = $request->execute($cacheRetention);
			foreach ($response->getAllData() as $data) {
				$result[] = $data;
			}
		}
		return new Response($this, [], $result);
	}

	/**
	 * 
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

}
