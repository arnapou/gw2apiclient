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
class ClientVersion2 extends AbstractClientVersion {

    protected $maxRequestIdsLength = 1000;

    /**
     * 
     * @param string $cachePrefix
     * @param array $ids
     * @return array
     */
    protected function preRequestSmartCaching($cachePrefix, &$ids) {
        $cache  = $this->getEnvironment()->getCache();
        $cached = [];
        if ($cache && $this->getEnvironment()->getUseSmartCaching()) {
            $requestedIds = [];
            foreach ($ids as $id) {
                $item = $cache->get($cachePrefix . '/' . $id);
                if (!empty($item) && is_array($item)) {
                    $cached[] = $item;
                }
                else {
                    $requestedIds[] = $id;
                }
            }
            $ids = $requestedIds;
        }
        return $cached;
    }

    /**
     * 
     * @param string $cachePrefix
     * @param array $results
     */
    protected function postRequestSmartCaching($cachePrefix, $results) {
        $cache = $this->getEnvironment()->getCache();
        if ($cache && $this->getEnvironment()->getUseSmartCaching()) {
            $retention = $this->getEnvironment()->getCacheRetention();
            foreach ($results as $item) {
                if (isset($item['id'])) {
                    $cache->set($cachePrefix . '/' . $item['id'], $item, $retention);
                }
            }
        }
    }

    /**
     * 
     * @param string $url
     * @param array $parameters
     * @param array $headers
     * @return array
     */
    protected function request($url, $parameters = array(), $headers = array()) {
        if (isset($parameters['ids']) && is_array($parameters['ids']) && !empty($parameters['ids'])) {
            $cachePrefix = __CLASS__ . $url;
            $results     = [];
            $ids         = $parameters['ids'];
            $cached      = $this->preRequestSmartCaching($cachePrefix, $ids);
            $length      = strlen(implode('xxx', $ids));
            $chunkNb     = ceil($length / $this->maxRequestIdsLength);

            if ($chunkNb) {
                $chunkSize = ceil(count($ids) / $chunkNb);
                $chunks    = array_chunk($ids, $chunkSize > 200 ? 200 : $chunkSize); // max 200 for chunk size

                foreach ($chunks as $chunk) {
                    $parameters['ids'] = $chunk;
                    $results           = array_merge($results, parent::request($url, $parameters, $headers));
                }
            }

            $this->postRequestSmartCaching($cachePrefix, $results);
            return array_merge($cached, $results);
        }
        else {
            return parent::request($url, $parameters, $headers);
        }
    }

    /**
     * 
     * @param string $url
     * @param array $parameters
     * @param array $headers
     * @return array
     */
    protected function requestAccessToken($url, $parameters = array(), $headers = array()) {
        if (empty($this->getEnvironment()->getAccessToken())) {
            throw new Exception('You should provide the access token before using this api.');
        }
        $parameters['access_token'] = $this->getEnvironment()->getAccessToken();
        return $this->request($url, $parameters, $headers);
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
     * @return array
     */
    public function apiAccount() {
        return $this->requestAccessToken('account');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountAchievements() {
        return $this->requestAccessToken('account/achievements');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountInventory() {
        return $this->requestAccessToken('account/inventory');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountBank() {
        return $this->requestAccessToken('account/bank');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountDyes() {
        return $this->requestAccessToken('account/dyes');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountTitles() {
        return $this->requestAccessToken('account/titles');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountMinis() {
        return $this->requestAccessToken('account/minis');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountFinishers() {
        return $this->requestAccessToken('account/finishers');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountMaterials() {
        return $this->requestAccessToken('account/materials');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountSkins() {
        return $this->requestAccessToken('account/skins');
    }

    /**
     * 
     * @return array
     */
    public function apiAccountWallet() {
        return $this->requestAccessToken('account/wallet');
    }

    /**
     * 
     * @return array
     */
    public function apiPvpStats() {
        return $this->requestAccessToken('pvp/stats');
    }

    /**
     * 
     * @return array
     */
    public function apiPvpStandings() {
        return $this->requestAccessToken('pvp/standings');
    }

    /**
     * 
     * @return array
     */
    public function apiPvpGames($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->requestAccessToken('pvp/games', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiPvpAmulets($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('pvp/amulets', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiPvpSeasons($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('pvp/seasons', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiBuild() {
        return $this->request('build');
    }

    /**
     * 
     * @return array
     */
    public function apiCharacters($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->requestAccessToken('characters', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiColors($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('colors', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceExchange() {
        return $this->request('commerce/exchange');
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceExchangeCoins($quantity) {
        return $this->request('commerce/exchange/coins', ['quantity' => $quantity]);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceExchangeGems($quantity) {
        return $this->request('commerce/exchange/gems', ['quantity' => $quantity]);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceListings($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('commerce/listings', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommercePrices($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('commerce/prices', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceTransactionsCurrentBuys() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->requestAccessToken('commerce/transactions/current/buys', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceTransactionsCurrentSells() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->requestAccessToken('commerce/transactions/current/sells', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceTransactionsHistoryBuys() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->requestAccessToken('commerce/transactions/history/buys', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiCommerceTransactionsHistorySells() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->requestAccessToken('commerce/transactions/history/sells', $parameters);
    }

    /**
     * 
     * @return array
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
     * @return array
     */
    public function apiCurrencies($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('currencies', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiFiles($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('files', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiAchievements($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('achievements', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiAchievementsDaily() {
        return $this->request('achievements/daily');
    }

    /**
     * 
     * @return array
     */
    public function apiAchievementsDailyTomorrow() {
        return $this->request('achievements/daily/tomorrow');
    }

    /**
     * 
     * @return array
     */
    public function apiAchievementsGroups($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('achievements/groups', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiAchievementsCategories($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('achievements/categories', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiBackstoryAnswers($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('backstory/answers', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiBackstoryQuestions($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('backstory/questions', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiItems($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('items', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiItemstats($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('itemstats', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiEmblem($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('emblem', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiGuildUpgrades($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('guild/upgrades', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiLegends($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('legends', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiFinishers($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('finishers', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiMinis($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('minis', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiPets($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('pets', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiMaps($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('maps', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiMaterials($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('materials', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiQuaggans($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('quaggans', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiRecipes($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('recipes', $parameters);
    }

    /**
     * 
     * @return array
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
     * @return array
     */
    public function apiSkins($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('skins', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiSkills($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('skills', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiSpecializations($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('specializations', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiTokeninfo() {
        return $this->requestAccessToken('tokeninfo');
    }

    /**
     * 
     * @return array
     */
    public function apiTitles($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('titles', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiStories($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('stories', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiStoriesSeasons($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('stories/seasons', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiTraits($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('traits', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiWorlds($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('worlds', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiWvwObjectives($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('wvw/objectives', $parameters);
    }

    /**
     * 
     * @return array
     */
    public function apiWvwMatches($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('wvw/matches', $parameters);
    }

}
