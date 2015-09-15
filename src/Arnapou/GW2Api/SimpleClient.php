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

use Arnapou\GW2Api\Cache\AbstractCacheDecorator;
use Arnapou\GW2Api\Cache\CacheInterface;
use Arnapou\GW2Api\Cache\MultipleGetCacheInterface;
use Arnapou\GW2Api\Cache\MongoCache;
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
 * @method array v2_account_dyes()
 * @method array v2_account_materials()
 * @method array v2_account_skins()
 * @method array v2_account_wallet()
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
 * @method array v2_currencies($ids = null)
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
 * @method array v2_traits($ids = null)
 * @method array v2_worlds($ids = null)
 *
 */
class SimpleClient {

    /**
     * Apis with only 'ids' parameter
     *
     * @var array
     */
    protected $smartV2Apis = [
        'v2_characters'        => 'apicharacters',
        'v2_colors'            => 'apicolors',
        'v2_commerce_listings' => 'apicommercelistings',
        'v2_commerce_prices'   => 'apicommerceprices',
        'v2_currencies'        => 'apicurrencies',
        'v2_files'             => 'apifiles',
        'v2_items'             => 'apiitems',
        'v2_maps'              => 'apimaps',
        'v2_materials'         => 'apimaterials',
        'v2_quaggans'          => 'apiquaggans',
        'v2_recipes'           => 'apirecipes',
        'v2_skins'             => 'apiskins',
        'v2_specializations'   => 'apispecializations',
        'v2_traits'            => 'apitraits',
        'v2_worlds'            => 'apiworlds',
    ];

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
     * @var Core\RequestManager
     */
    protected $requestManager;

    /**
     *
     * @var array
     */
    protected $clones = [];

    /**
     * 
     * @param RequestManager $requestManager
     * @param string $lang
     */
    public function __construct(Core\RequestManager $requestManager, $lang) {
        $this->requestManager = $requestManager;

        $this->clientV1 = new Core\ClientV1($requestManager);
        $this->clientV1->setLang($lang);

        $this->clientV2 = new Core\ClientV2($requestManager);
        $this->clientV2->setLang($lang);
    }

    /**
     * 
     * @param string $lang
     */
    public function setLang($lang) {
        $this->clientV1->setLang($lang);
        $this->clientV2->setLang($lang);
    }

    /**
     * 
     * @return string
     */
    public function getLang() {
        return $this->clientV2->getLang();
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
     * @param string|CacheInterface $cache
     * @return SimpleClient
     */
    public static function create($lang, $cache = null) {
        $requestManager = new Core\RequestManager();
        $client         = new static($requestManager, $lang);
        if ($cache) {
            if ($cache instanceof CacheInterface) {
                $requestManager->setCache($cache);
            }
            else {
                $requestManager->setCache(new Cache\MemoryCacheDecorator(new Cache\FileCache($cache)));
            }
        }
        return $client;
    }

    /**
     * 
     * @param string|CacheInterface $cache
     * @return SimpleClient
     */
    public static function createDE($cache) {
        return static::create(Core\AbstractClient::LANG_DE, $cache);
    }

    /**
     * 
     * @param string|CacheInterface $cache
     * @return SimpleClient
     */
    public static function createEN($cache) {
        return static::create(Core\AbstractClient::LANG_EN, $cache);
    }

    /**
     * 
     * @param string|CacheInterface $cache
     * @return SimpleClient
     */
    public static function createES($cache) {
        return static::create(Core\AbstractClient::LANG_ES, $cache);
    }

    /**
     * 
     * @param string|CacheInterface $cache
     * @return SimpleClient
     */
    public static function createFR($cache) {
        return static::create(Core\AbstractClient::LANG_FR, $cache);
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

    /**
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (preg_match('!^v([12])_([a-z0-9_]+)$!', $name, $m)) {
            if (isset($this->smartV2Apis[$name]) && isset($arguments[0]) && $this->requestManager->hasCache()) {
                $retention = $this->requestManager->getCacheRetention('/' . str_replace('_', '/', $name));
                return $this->smartV2Caching($this->smartV2Apis[$name], $arguments[0], $retention);
            }
            if ($m[1] == 1) {
                $client = $this->getClientV1();
            }
            elseif ($m[1] == 2) {
                $client = $this->getClientV2();
            }
            $method = 'api' . str_replace('_', '', $m[2]);
            if (method_exists($client, $method)) {
                $request = call_user_func_array([$client, $method], $arguments); /* @var $request Core\RequestInterface */
                return $request->execute()->getData();
            }
        }
        throw new Exception('Unknown method ' . $name);
    }

    /**
     * This "smart" method is able to pick up single items already in cache and calling the remaining in one call
     * in order to optimize performance.
     * 
     * If you use MongoCache, it is optimized a step further because it can request all items directly in one mongo request.
     * 
     * @param string $method
     * @param array $ids
     * @param int $retention
     * @return mixed
     */
    protected function smartV2Caching($method, $ids, $retention) {
        if (empty($ids)) {
            return [];
        }
        $clientV2    = $this->getClientV2();
        $cache       = $this->requestManager->getCache();
        $cachePrefix = 'smartCaching/' . $clientV2->getLang() . '_' . substr($method, 3) . '/';
        $pk          = ($method == 'apicharacters') ? 'name' : 'id';

        // single id
        if (!is_array($ids)) {
            $result = $cache->get($cachePrefix . $ids);
            if (!empty($result)) {
                return [$ids => $result];
            }
            return $this->smartV2Caching($method, [$ids], $retention);
        }

        // multiple ids
        $ids = array_unique($ids);

        $objectsFromCache = [];
        $idsToRequest     = [];
        if ($cache instanceof MultipleGetCacheInterface) {
            $foundIds = [];
            foreach ($cache->getMultiple($ids, $cachePrefix) as $result) {
                if (isset($result[$pk])) {
                    $objectsFromCache[$result[$pk]] = $result;
                    $foundIds[]                     = $result[$pk];
                }
            }
            $idsToRequest = array_diff($ids, $foundIds);
        }
        else {
            foreach ($ids as $id) {
                $result = $cache->get($cachePrefix . $id);
                if (is_array($result)) {
                    $objectsFromCache[$id] = $result;
                }
                else {
                    $idsToRequest[] = $id;
                }
            }
        }
        $return = $objectsFromCache;
        if (!empty($idsToRequest)) {
            $objects     = $clientV2->$method($idsToRequest)->execute($retention)->getAllData();
            $responseIds = [];
            foreach ($objects as $object) {
                if (isset($object[$pk])) {
                    $cache->set($cachePrefix . $object[$pk], $object, $retention);
                    $return[$object[$pk]] = $object;
                    $responseIds[]        = $object[$pk];
                }
            }
            if (empty($responseIds)) {
                $notFoundIds = $idsToRequest;
            }
            else {
                $notFoundIds = array_diff($idsToRequest, $responseIds);
            }
            if (!empty($notFoundIds)) {
                foreach ($notFoundIds as $id) {
                    $cache->set($cachePrefix . $id, [$pk => $id], $retention);
                    $return[$id] = [$pk => $id];
                }
            }
        }
        return $return;
    }

}
