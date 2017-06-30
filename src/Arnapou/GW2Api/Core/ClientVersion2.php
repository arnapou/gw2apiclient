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
class ClientVersion2 extends AbstractClientVersion
{

    protected $maxRequestIdsLength = 1000;

    /**
     *
     * @param string $cachePrefix
     * @param array  $ids
     * @return array
     */
    protected function preRequestSmartCaching($cachePrefix, &$ids)
    {
        $cache  = $this->getEnvironment()->getCache();
        $cached = [];
        if ($cache && $this->getEnvironment()->getUseSmartCaching()) {
            $requestedIds = [];
            foreach ($ids as $id) {
                $item = $cache->get($cachePrefix . '/' . $id);
                if (!empty($item) && is_array($item)) {
                    $cached[] = $item;
                } else {
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
     * @param array  $results
     */
    protected function postRequestSmartCaching($cachePrefix, $results)
    {
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
     * @param array  $parameters
     * @param array  $headers
     * @return array
     */
    protected function request($url, $parameters = [], $headers = [])
    {
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
        } else {
            return parent::request($url, $parameters, $headers);
        }
    }

    /**
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     * @return array
     */
    protected function requestAccessToken($url, $parameters = [], $headers = [])
    {
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
    public function getBaseUrl()
    {
        return 'https://api.guildwars2.com/v2/';
    }

    /**
     *
     * @return array
     */
    public function apiAccount()
    {
        return $this->requestAccessToken('account');
    }

    /**
     *
     * @return array
     */
    public function apiAccountAchievements()
    {
        return $this->requestAccessToken('account/achievements');
    }

    /**
     *
     * @return array
     */
    public function apiAccountInventory()
    {
        return $this->requestAccessToken('account/inventory');
    }

    /**
     *
     * @return array
     */
    public function apiAccountBank()
    {
        return $this->requestAccessToken('account/bank');
    }

    /**
     *
     * @return array
     */
    public function apiAccountDyes()
    {
        return $this->requestAccessToken('account/dyes');
    }

    /**
     *
     * @return array
     */
    public function apiAccountTitles()
    {
        return $this->requestAccessToken('account/titles');
    }

    /**
     *
     * @return array
     */
    public function apiAccountMinis()
    {
        return $this->requestAccessToken('account/minis');
    }

    /**
     *
     * @return array
     */
    public function apiAccountGliders()
    {
        return $this->requestAccessToken('account/gliders');
    }

    /**
     *
     * @return array
     */
    public function apiAccountFinishers()
    {
        return $this->requestAccessToken('account/finishers');
    }

    /**
     *
     * @return array
     */
    public function apiAccountOutfits()
    {
        return $this->requestAccessToken('account/outfits');
    }

    /**
     *
     * @return array
     */
    public function apiAccountMasteryPoints()
    {
        return $this->requestAccessToken('account/mastery/points');
    }

    /**
     *
     * @return array
     */
    public function apiAccountMasteries()
    {
        return $this->requestAccessToken('account/masteries');
    }

    /**
     *
     * @return array
     */
    public function apiAccountMailcarriers()
    {
        return $this->requestAccessToken('account/mailcarriers');
    }

    /**
     *
     * @return array
     */
    public function apiAccountMaterials()
    {
        return $this->requestAccessToken('account/materials');
    }

    /**
     *
     * @return array
     */
    public function apiAccountSkins()
    {
        return $this->requestAccessToken('account/skins');
    }

    /**
     *
     * @return array
     */
    public function apiAccountWallet()
    {
        return $this->requestAccessToken('account/wallet');
    }

    /**
     *
     * @return array
     */
    public function apiAccountDungeons()
    {
        return $this->requestAccessToken('account/dungeons');
    }

    /**
     *
     * @return array
     */
    public function apiAccountPvpHeroes()
    {
        return $this->requestAccessToken('account/pvp/heroes');
    }

    /**
     *
     * @return array
     */
    public function apiAccountRaids()
    {
        return $this->requestAccessToken('account/raids');
    }

    /**
     *
     * @return array
     */
    public function apiAccountRecipes()
    {
        return $this->requestAccessToken('account/recipes');
    }

    /**
     *
     * @return array
     */
    public function apiAccountHomeCats()
    {
        return $this->requestAccessToken('account/home/cats');
    }

    /**
     *
     * @return array
     */
    public function apiAccountHomeNodes()
    {
        return $this->requestAccessToken('account/home/nodes');
    }

    /**
     *
     * @return array
     */
    public function apiPvpStats()
    {
        return $this->requestAccessToken('pvp/stats');
    }

    /**
     *
     * @return array
     */
    public function apiPvpStandings()
    {
        return $this->requestAccessToken('pvp/standings');
    }

    /**
     *
     * @return array
     */
    public function apiPvpGames($ids = null)
    {
        return $this->requestAccessToken('pvp/games', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiPvpHeroes($ids = null)
    {
        return $this->requestAccessToken('pvp/heroes', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiPvpRanks($ids = null)
    {
        return $this->requestAccessToken('pvp/ranks', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiPvpAmulets($ids = null)
    {
        return $this->request('pvp/amulets', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiPvpSeasons($ids = null)
    {
        return $this->request('pvp/seasons', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiBuild()
    {
        return $this->request('build');
    }

    /**
     *
     * @return array
     */
    public function apiCharacters($ids = null)
    {
        return $this->requestAccessToken('characters', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiColors($ids = null)
    {
        return $this->request('colors', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiDungeons($ids = null)
    {
        return $this->request('dungeons', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiRaids($ids = null)
    {
        return $this->request('raids', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiCommerceDelivery()
    {
        return $this->request('commerce/delivery');
    }

    /**
     *
     * @return array
     */
    public function apiCommerceExchange()
    {
        return $this->request('commerce/exchange');
    }

    /**
     *
     * @return array
     */
    public function apiCommerceExchangeCoins($quantity)
    {
        return $this->request('commerce/exchange/coins', ['quantity' => $quantity]);
    }

    /**
     *
     * @return array
     */
    public function apiCommerceExchangeGems($quantity)
    {
        return $this->request('commerce/exchange/gems', ['quantity' => $quantity]);
    }

    /**
     *
     * @return array
     */
    public function apiCommerceListings($ids = null)
    {
        return $this->request('commerce/listings', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiCommercePrices($ids = null)
    {
        return $this->request('commerce/prices', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiCommerceTransactionsCurrentBuys()
    {
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
    public function apiCommerceTransactionsCurrentSells()
    {
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
    public function apiCommerceTransactionsHistoryBuys()
    {
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
    public function apiCommerceTransactionsHistorySells()
    {
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
    public function apiContinents($continentId = null, $floorId = null, $regionId = null, $mapId = null)
    {
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
    public function apiCats($ids = null)
    {
        return $this->request('cats', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiCurrencies($ids = null)
    {
        return $this->request('currencies', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiFiles($ids = null)
    {
        return $this->request('files', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiAchievements($ids = null)
    {
        return $this->request('achievements', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiAchievementsDaily()
    {
        return $this->request('achievements/daily');
    }

    /**
     *
     * @return array
     */
    public function apiAchievementsDailyTomorrow()
    {
        return $this->request('achievements/daily/tomorrow');
    }

    /**
     *
     * @return array
     */
    public function apiAchievementsGroups($ids = null)
    {
        return $this->request('achievements/groups', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiAchievementsCategories($ids = null)
    {
        return $this->request('achievements/categories', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiBackstoryAnswers($ids = null)
    {
        return $this->request('backstory/answers', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiBackstoryQuestions($ids = null)
    {
        return $this->request('backstory/questions', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiItems($ids = null)
    {
        return $this->request('items', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiItemstats($ids = null)
    {
        return $this->request('itemstats', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiEmblem($ids = null)
    {
        return $this->request('emblem', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     * !!!
     * this function has two output depending on the parameters,
     * if you pass a guild id to this function, it will retrieve
     * the specific guild upgrades list
     * !!!
     * @return array
     */
    public function apiGuildUpgrades($ids = null)
    {
        if (is_string($ids) && preg_match('!^([A-Z0-9]+-)+[A-Z0-9]+$!si', $ids)) {
            return $this->requestAccessToken('guild/' . $ids . '/upgrades');
        }
        return $this->request('guild/upgrades', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiGuildPermissions($ids = null)
    {
        return $this->request('guild/permissions', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiGuild($guildId)
    {
        if ($this->getEnvironment()->getAccessToken()) {
            return $this->requestAccessToken('guild/' . $guildId);
        } else {
            return $this->request('guild/' . $guildId);
        }
    }

    /**
     *
     * @return array
     */
    public function apiGuildLog($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/log');
    }

    /**
     *
     * @return array
     */
    public function apiGuildMembers($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/members');
    }

    /**
     *
     * @return array
     */
    public function apiGuildRanks($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/ranks');
    }

    /**
     *
     * @return array
     */
    public function apiGuildStash($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/stash');
    }

    /**
     *
     * @return array
     */
    public function apiGuildTeams($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/teams');
    }

    /**
     *
     * @return array
     */
    public function apiGuildTreasury($guildId)
    {
        return $this->requestAccessToken('guild/' . $guildId . '/treasury');
    }

    /**
     *
     * @return array
     */
    public function apiLegends($ids = null)
    {
        return $this->request('legends', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiGliders($ids = null)
    {
        return $this->request('gliders', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiFinishers($ids = null)
    {
        return $this->request('finishers', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiOutfits($ids = null)
    {
        return $this->request('outfits', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiMasteries($ids = null)
    {
        return $this->request('masteries', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiMinis($ids = null)
    {
        return $this->request('minis', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiNodes($ids = null)
    {
        return $this->request('nodes', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiProfessions($ids = null)
    {
        return $this->request('professions', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiPets($ids = null)
    {
        return $this->request('pets', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiMaps($ids = null)
    {
        return $this->request('maps', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiMailcarriers($ids = null)
    {
        return $this->request('mailcarriers', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiMaterials($ids = null)
    {
        return $this->request('materials', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiQuaggans($ids = null)
    {
        return $this->request('quaggans', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiRecipes($ids = null)
    {
        return $this->request('recipes', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiRecipesSearch($input = null, $output = null)
    {
        $parameters = [];
        if ($input) {
            $parameters['input'] = $input;
        } elseif ($output) {
            $parameters['output'] = $output;
        } else {
            throw new Exception('Either input or output parameter should be specified.');
        }
        return $this->request('recipes/search', $parameters);
    }

    /**
     *
     * @return array
     */
    public function apiSkins($ids = null)
    {
        return $this->request('skins', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiRaces($ids = null)
    {
        return $this->request('races', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiSkills($ids = null)
    {
        return $this->request('skills', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiSpecializations($ids = null)
    {
        return $this->request('specializations', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiTokeninfo()
    {
        return $this->requestAccessToken('tokeninfo');
    }

    /**
     *
     * @return array
     */
    public function apiTitles($ids = null)
    {
        return $this->request('titles', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiStories($ids = null)
    {
        return $this->request('stories', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiStoriesSeasons($ids = null)
    {
        return $this->request('stories/seasons', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiTraits($ids = null)
    {
        return $this->request('traits', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWorlds($ids = null)
    {
        return $this->request('worlds', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWvwObjectives($ids = null)
    {
        return $this->request('wvw/objectives', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWvwRanks($ids = null)
    {
        return $this->request('wvw/ranks', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWvwUpgrades($ids = null)
    {
        return $this->request('wvw/upgrades', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWvwAbilities($ids = null)
    {
        return $this->request('wvw/abilities', empty($ids) ? [] : ['ids' => $ids]);
    }

    /**
     *
     * @return array
     */
    public function apiWvwMatches($ids = null)
    {
        return $this->request('wvw/matches', empty($ids) ? [] : ['ids' => $ids]);
    }
}
