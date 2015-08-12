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

use Arnapou\GW2Api\Exception\RequestException;

abstract class AbstractClient {

	const LANG_DE = 'de';
	const LANG_EN = 'en';
	const LANG_ES = 'es';
	const LANG_FR = 'fr';

	/**
	 *
	 * @var string
	 */
	protected $lang = null;

	/**
	 *
	 * @var RequestManager
	 */
	protected $requestManager = null;

	/**
	 * 
	 */
	public function __construct(RequestManager $requestManager = null) {
		if ($requestManager === null) {
			$this->requestManager = new RequestManager();
		}
		else {
			$this->requestManager = $requestManager;
		}
	}

	/**
	 * 
	 * @return RequestManager
	 */
	public function getRequestManager() {
		return $this->requestManager;
	}

	/**
	 * 
	 * @return string base url with ending slash
	 */
	abstract public function getBaseUrl();

	/**
	 * 
	 * @param array $parameters
	 */
	protected function checkParameters(&$parameters) {
		if (!isset($parameters['lang']) && !empty($this->lang)) {
			$parameters['lang'] = $this->lang;
		}
	}

	/**
	 * 
	 * @param string $url
	 * @param array $parameters
	 * @param array $headers
	 * @return RequestInterface
	 */
	protected function request($url, $parameters = [], $headers = []) {
		$this->checkParameters($parameters);
		return new Request($this->requestManager, $this->getBaseUrl() . $url, $parameters, $headers);
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

}
