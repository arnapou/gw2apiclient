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

    protected function request($url, $parameters = array(), $headers = array()) {
        if (isset($parameters['ids']) && is_array($parameters['ids']) && count($parameters['ids']) > 100) {
            $this->checkParameters($parameters);

            $request = new RequestBatch($this->requestManager, $this->getBaseUrl() . $url, $headers);
            $chunks  = array_chunk($parameters['ids'], 100);
            foreach ($chunks as $chunk) {
                $parameters['ids'] = $chunk;
                $request->addParameterSet($parameters);
            }
            return $request;
        }
        else {
            return parent::request($url, $parameters, $headers);
        }
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
     * @return RequestInterface
     */
    public function apiAccount() {
        return $this->checkAccessToken($this->request('account'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiAccountBank() {
        return $this->checkAccessToken($this->request('account/bank'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiAccountDyes() {
        return $this->checkAccessToken($this->request('account/dyes'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiAccountMaterials() {
        return $this->checkAccessToken($this->request('account/materials'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiAccountSkins() {
        return $this->checkAccessToken($this->request('account/skins'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiAccountWallet() {
        return $this->checkAccessToken($this->request('account/wallet'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiPvpStats() {
        return $this->checkAccessToken($this->request('pvp/stats'));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiPvpGames($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->checkAccessToken($this->request('pvp/games', $parameters));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiBuild() {
        return $this->request('build');
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCharacters($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->checkAccessToken($this->request('characters', $parameters));
    }

    /**
     * 
     * @return RequestInterface
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
     * @return RequestInterface
     */
    public function apiCommerceExchange() {
        return $this->request('commerce/exchange');
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCommerceExchangeCoins($quantity) {
        return $this->request('commerce/exchange/coins', ['quantity' => $quantity]);
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCommerceExchangeGems($quantity) {
        return $this->request('commerce/exchange/gems', ['quantity' => $quantity]);
    }

    /**
     * 
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
     */
    public function apiCommerceTransactionsCurrentBuys() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->checkAccessToken($this->request('commerce/transactions/current/buys', $parameters));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCommerceTransactionsCurrentSells() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->checkAccessToken($this->request('commerce/transactions/current/sells', $parameters));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCommerceTransactionsHistoryBuys() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->checkAccessToken($this->request('commerce/transactions/history/buys', $parameters));
    }

    /**
     * 
     * @return RequestInterface
     */
    public function apiCommerceTransactionsHistorySells() {
        $parameters = [
            'page_size' => 200,
            'page'      => 0,
        ];
        return $this->checkAccessToken($this->request('commerce/transactions/history/sells', $parameters));
    }

    /**
     * 
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
     */
    public function apiTokeninfo() {
        return $this->checkAccessToken($this->request('tokeninfo'));
    }

    /**
     * 
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
     */
    public function apiWvwObjectives($ids = null) {
        $parameters = [];
        if (!empty($ids)) {
            $parameters['ids'] = $ids;
        }
        return $this->request('wvw/objectives', $parameters);
    }

}
