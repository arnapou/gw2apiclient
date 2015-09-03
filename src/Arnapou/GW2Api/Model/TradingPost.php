<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Model;

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class TradingPost extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $currentBuys;

    /**
     *
     * @var array
     */
    protected $currentSells;

    /**
     *
     * @var array
     */
    protected $historyBuys;

    /**
     *
     * @var array
     */
    protected $historySells;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client) {
        parent::__construct($client);
    }

    /**
     * 
     * @param array $items
     * @return array
     */
    protected function prepareItems($items) {
        if (empty($items)) {
            return [];
        }
        $return = [];
        $ids    = [];
        foreach ($items as $item) {
            $ids[] = $item['item_id'];
        }
        $this->prepareItemIds($ids);
        $this->prepareFlush();

        foreach ($items as $item) {
            $key = $item['item_id'] . '_' . $item['price'];
            if (isset($return[$key])) {
                $return[$key]['quantity'] += $item['quantity'];
            }
            else {
                $item['item'] = new Item($this->client, $item['item_id']);
                unset($item['item_id']);
                $return[$key] = $item;
            }
        }
        return array_values($return);
    }

    /**
     * 
     * @param string $method
     * @param integer $nbpages
     * @return array
     */
    protected function apiCommerceGetData($method, $nbpages = 2) {
        $response = $this->client->getClientV2()->$method()->execute();
        $alldata  = $response->getData();
        while ($response->hasNextPage() && $nbpages > 1) {
            $response = $response->getNextPage();
            foreach ($response->getData() as $item) {
                $alldata[] = $item;
            }
            $nbpages--;
        }
        return $alldata;
    }

    /**
     * 
     * @param int $nbpages
     * @return array
     */
    public function getCurrentBuys($nbpages = 2) {
        if (!isset($this->currentBuys)) {
            $items             = $this->apiCommerceGetData('apiCommerceTransactionsCurrentBuys', $nbpages);
            $this->currentBuys = $this->prepareItems($items);
        }
        return $this->currentBuys;
    }

    /**
     * 
     * @param int $nbpages
     * @return array
     */
    public function getCurrentSells($nbpages = 2) {
        if (!isset($this->currentSells)) {
            $items              = $this->apiCommerceGetData('apiCommerceTransactionsCurrentSells', $nbpages);
            $this->currentSells = $this->prepareItems($items);
        }
        return $this->currentSells;
    }

    /**
     * 
     * @param int $nbpages
     * @return array
     */
    public function getHistoryBuys($nbpages = 2) {
        if (!isset($this->historyBuys)) {
            $items             = $this->apiCommerceGetData('apiCommerceTransactionsHistoryBuys', $nbpages);
            $this->historyBuys = $this->prepareItems($items);
        }
        return $this->historyBuys;
    }

    /**
     * 
     * @param int $nbpages
     * @return array
     */
    public function getHistorySells($nbpages = 2) {
        if (!isset($this->historySells)) {
            $items              = $this->apiCommerceGetData('apiCommerceTransactionsHistorySells', $nbpages);
            $this->historySells = $this->prepareItems($items);
        }
        return $this->historySells;
    }

}
