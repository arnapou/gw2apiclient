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

interface RequestInterface {

	/**
	 * 
	 * @return Response
	 */
	public function execute($cacheRetention = null);

	/**
	 * 
	 * @param string $param
	 * @return integer
	 */
	public function getParameter($param);

	/**
	 * 
	 * @param string $param
	 * @param string $value
	 * @return RequestInterface
	 */
	public function setParameter($param, $value);

	/**
	 * 
	 * @return string
	 */
	public function getUrl();
}
