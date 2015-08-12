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

class Response {

	/**
	 *
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 *
	 * @var array
	 */
	protected $headers;

	/**
	 *
	 * @var array
	 */
	protected $data;

	/**
	 *
	 * @var int
	 */
	protected $page;

	/**
	 *
	 * @var int
	 */
	protected $resultTotal;

	/**
	 *
	 * @var int
	 */
	protected $resultCount;

	/**
	 *
	 * @var int
	 */
	protected $pageTotal = 1;

	/**
	 *
	 * @var int
	 */
	protected $pageSize;

	/**
	 * 
	 */
	public function __construct(RequestInterface $request, $headers, $data) {
		$this->request = $request;
		$this->data = $data;
		$this->headers = $headers;

		$this->page = $request->getParameter('page');
		if (empty($this->page)) {
			$this->page = 0;
		}

		/*
		 * [x-page-total] => 35
		 * [x-page-size] => 50
		 * [x-result-total] => 1710
		 * [x-result-count] => 50
		 */
		if (isset($headers['x-page-total'])) {
			$this->pageTotal = $headers['x-page-total'];
		}
		if (isset($headers['x-page-size'])) {
			$this->pageSize = $headers['x-page-size'];
		}
		if (isset($headers['x-result-total'])) {
			$this->resultTotal = $headers['x-result-total'];
		}
		if (isset($headers['x-result-count'])) {
			$this->resultCount = $headers['x-result-count'];
		}
	}

	public function count() {
		return isset($this->resultTotal) ? $this->resultTotal : count($this->data);
	}

	public function getPageTotal() {
		return $this->pageTotal;
	}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function getPage() {
		return $this->page;
	}

	public function hasNextPage() {
		if ($this->page + 1 < $this->pageTotal) {
			return true;
		}
		return null;
	}

	public function getNextPage() {
		if ($this->page + 1 < $this->pageTotal) {
			return $this->request->setParameter('page', $this->page + 1)->execute();
		}
		return null;
	}

	/**
	 * 
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * 
	 * @return array
	 */
	public function getAllData() {
		$alldata = $this->getData();
		$response = $this;
		while ($response->hasNextPage()) {
			$response = $this->getNextPage();
			foreach ($response->getData() as $item) {
				$alldata[] = $item;
			}
		}
		return $alldata;
	}

}
