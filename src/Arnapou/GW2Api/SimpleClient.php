<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api;

use Arnapou\GW2Api\Exception\Exception;

/**
 * Client to interact with AWS Directory Service
 *
 * @method array v1_build()
 * @method array v1_colors()
 * @method array v1_continents()
 * @method array v1_event_details($eventId = null)
 * @method array v1_event_names()
 * @method array v1_events($worldId = null, $mapId = null, $eventId = null) !!! DEPRECATED !!!
 * @method array v1_files()
 * @method array v1_guild_details($guildId = null, $guildName = null)
 * @method array v1_item_details($itemId = null)
 * @method array v1_items()
 * @method array v1_map_floor($continentId, $floor)
 * @method array v1_map_names()
 * @method array v1_maps($mapId = null)
 * @method array v1_recipe_details($recipeId)
 * @method array v1_recipes()
 * @method array v1_skin_details($skinId)
 * @method array v1_skins()
 * @method array v1_world_names()
 * @method array v1_wvw_match_details($matchId)
 * @method array v1_wvw_matches()
 * @method array v1_wvw_objective_names()
 * 
 * @method array v2_account()
 * @method array v2_account_bank()
 * @method array v2_account_materials()
 * @method array v2_build()
 * @method array v2_characters($ids = null)
 * @method array v2_colors($ids = null)
 * @method array v2_commerce_exchange()
 * @method array v2_commerce_exchange_coins($quantity)
 * @method array v2_commerce_exchange_gems($quantity)
 * @method array v2_commerce_listings($ids = null)
 * @method array v2_commerce_prices($ids = null)
 * @method array v2_commerce_transactions_current_buys()
 * @method array v2_commerce_transactions_current_sells()
 * @method array v2_commerce_transactions_history_buys()
 * @method array v2_commerce_transactions_history_sells()
 * @method array v2_continents($continentId = null, $floorId = null, $regionId = null, $mapId = null)
 * @method array v2_files($ids = null)
 * @method array v2_items($ids = null)
 * @method array v2_maps($ids = null)
 * @method array v2_materials($ids = null)
 * @method array v2_quaggans($ids = null)
 * @method array v2_recipes($ids = null)
 * @method array v2_recipes_search($input = null, $output = null)
 * @method array v2_skins($ids = null)
 * @method array v2_specializations($ids = null)
 * @method array v2_tokeninfo()
 * @method array v2_worlds($ids = null)
 *
 */
class SimpleClient {

	/**
	 *
	 * @var Core\ClientV1
	 */
	protected $clientV1;

	/**
	 *
	 * @var Core\ClientV2
	 */
	protected $clientV2;

	/**
	 * 
	 * @param RequestManager $requestManager
	 * @param string $lang
	 */
	private function __construct(Core\RequestManager $requestManager, $lang) {
		$this->clientV1 = new Core\ClientV1($requestManager);
		$this->clientV1->setLang($lang);

		$this->clientV2 = new Core\ClientV2($requestManager);
		$this->clientV2->setLang($lang);
	}

	/**
	 * 
	 * @param string $token
	 */
	public function setAccessToken($token) {
		$this->clientV2->setAccessToken($token);
	}

	/**
	 * 
	 * @param string $lang
	 * @param string $cachePath
	 * @return SimpleClient
	 */
	private static function create($lang, $cachePath) {
		$requestManager = new Core\RequestManager();
		$client = new static($requestManager, $lang);
		if ($cachePath !== null) {
			$cache = new Cache\MemoryCacheDecorator(new Cache\FileCache($cachePath));
			$requestManager->setCache($cache);
		}
		return $client;
	}

	/**
	 * 
	 * @param string $cachePath
	 * @return SimpleClient
	 */
	public static function DE($cachePath) {
		return static::create(Core\AbstractClient::LANG_DE, $cachePath);
	}

	/**
	 * 
	 * @param string $cachePath
	 * @return SimpleClient
	 */
	public static function EN($cachePath) {
		return static::create(Core\AbstractClient::LANG_EN, $cachePath);
	}

	/**
	 * 
	 * @param string $cachePath
	 * @return SimpleClient
	 */
	public static function ES($cachePath) {
		return static::create(Core\AbstractClient::LANG_ES, $cachePath);
	}

	/**
	 * 
	 * @param string $cachePath
	 * @return SimpleClient
	 */
	public static function FR($cachePath) {
		return static::create(Core\AbstractClient::LANG_FR, $cachePath);
	}

	/**
	 * 
	 * @return Core\ClientV1
	 */
	public function getClientV1() {
		return $this->clientV1;
	}

	/**
	 * 
	 * @return Core\ClientV2
	 */
	public function getClientV2() {
		return $this->clientV2;
	}

	public function __call($name, $arguments) {
		if (preg_match('!^v([12])_([a-z0-9_]+)$!', $name, $m)) {
			if ($m[1] == 1) {
				$client = $this->getClientV1();
			}
			elseif ($m[1] == 2) {
				$client = $this->getClientV2();
			}
			$method = 'api' . str_replace('_', '', str_replace('/', '', $m[2]));
			if (method_exists($client, $method)) {
				$request = call_user_func_array([$client, $method], $arguments); /* @var $request Core\RequestInterface */
				return $request->execute()->getData();
			}
		}
		throw new Exception('Uknown method ' . $name);
	}

}
