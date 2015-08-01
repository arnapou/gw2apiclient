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

use Arnapou\GW2Api\Exception\Exception;

/**
 * 
 * @doc https://wiki.guildwars2.com/wiki/API:2
 */
class ClientV2 extends AbstractClient {

	/**
	 *
	 * @var string
	 */
	protected $accessToken;

	/**
	 * 
	 * @return string
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * 
	 * @param string $token
	 */
	public function setAccessToken($token) {
		$this->accessToken = $token;
	}

	protected function checkAccessToken(Request $request) {
		if (empty($this->accessToken)) {
			throw new Exception('You should provide the access token before using this api.');
		}
		return $request->setParameter('access_token', $this->accessToken);
	}

	/**
	 * 
	 * @return string
	 */
	public function getBaseUrl() {
		return 'https://api.guildwars2.com/v2/';
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiAccount() {
		return $this->checkAccessToken($this->request('account'));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiAccountBank() {
		return $this->checkAccessToken($this->request('account/bank'));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiAccountMaterials() {
		return $this->checkAccessToken($this->request('account/materials'));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiBuild() {
		return $this->request('build');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCharacters($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->checkAccessToken($this->request('characters', $parameters));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiColors($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('colors', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceExchange() {
		return $this->request('commerce/exchange');
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceExchangeCoins($quantity) {
		return $this->request('commerce/exchange/coins', ['quantity' => $quantity]);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceExchangeGems($quantity) {
		return $this->request('commerce/exchange/gems', ['quantity' => $quantity]);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceListings($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('commerce/listings', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommercePrices($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('commerce/prices', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceTransactionsCurrentBuys() {
		$parameters = [
			'page_size'	 => 200,
			'page'		 => 0,
		];
		return $this->checkAccessToken($this->request('commerce/transactions/current/buys', $parameters));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceTransactionsCurrentSells() {
		$parameters = [
			'page_size'	 => 200,
			'page'		 => 0,
		];
		return $this->checkAccessToken($this->request('commerce/transactions/current/sells', $parameters));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceTransactionsHistoryBuys() {
		$parameters = [
			'page_size'	 => 200,
			'page'		 => 0,
		];
		return $this->checkAccessToken($this->request('commerce/transactions/history/buys', $parameters));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiCommerceTransactionsHistorySells() {
		$parameters = [
			'page_size'	 => 200,
			'page'		 => 0,
		];
		return $this->checkAccessToken($this->request('commerce/transactions/history/sells', $parameters));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiContinents($continentId = null, $floorId = null, $regionId = null, $mapId = null) {
		$url = 'continents';
		if ($continentId) {
			$url .= '/' . $continentId;
			if ($floorId) {
				$url .= '/floors/' . $floorId;
				if ($regionId) {
					$url .= '/regions/' . $regionId;
					if ($mapId) {
						$url .= '/maps/' . $mapId;
					}
				}
			}
		}
		return $this->request($url);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiFiles($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('files', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiItems($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('items', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiMaps($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('maps', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiMaterials($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('materials', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiQuaggans($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('quaggans', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiRecipes($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('recipes', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiRecipesSearch($input = null, $output = null) {
		$parameters = [];
		if ($input) {
			$parameters['input'] = $input;
		}
		elseif ($output) {
			$parameters['output'] = $output;
		}
		else {
			throw new Exception('Either input or output parameter should be specified.');
		}
		return $this->request('recipes/search', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiSkins($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('skins', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiSpecializations($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('specializations', $parameters);
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiTokeninfo() {
		return $this->checkAccessToken($this->request('tokeninfo'));
	}

	/**
	 * 
	 * @return Request
	 */
	public function apiWorlds($ids = null) {
		$parameters = [];
		if (is_array($ids)) {
			$parameters['ids'] = implode(',', $ids);
		}
		elseif (!empty($ids)) {
			$parameters['ids'] = $ids;
		}
		return $this->request('worlds', $parameters);
	}

}
