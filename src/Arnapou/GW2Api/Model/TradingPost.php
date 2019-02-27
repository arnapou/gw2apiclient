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

class TradingPost extends AbstractObject
{
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
     * @var array
     */
    protected $delivery;

    /**
     *
     * @var array
     */
    protected $deliveryItems;

    /**
     *
     * @param array $items
     * @return array
     */
    protected function prepareItems($items)
    {
        if (empty($items)) {
            return [];
        }
        $return = [];
        $env    = $this->getEnvironment();

        foreach ($items as $item) {
            $key = $item['item_id'] . '_' . $item['price'];
            if (isset($return[$key])) {
                $return[$key]['quantity'] += $item['quantity'];
            } else {
                $item['item'] = new Item($env, $item['item_id']);
                unset($item['item_id']);
                $return[$key] = $item;
            }
        }
        return array_values($return);
    }

    
    protected function checkDelivery()
    {
        if (!isset($this->delivery)) {
            $this->delivery = $this->getEnvironment()->getClientVersion2()->apiCommerceDelivery();
        }
    }

    /**
     * @return mixed|null
     */
    public function getDeliveryAmount()
    {
        $this->checkDelivery();
        return isset($this->delivery['coins']) ? $this->delivery['coins'] : null;
    }

    /**
     * @return mixed|null
     */
    public function getDeliveryItems()
    {
        $this->checkDelivery();
        if (!isset($this->deliveryItems)) {
            $this->deliveryItems = [];
            if (isset($this->delivery['items']) && \is_array($this->delivery['items'])) {
                $items = [];
                foreach ($this->delivery['items'] as $item) {
                    if (isset($item['id'], $item['count'])) {
                        if (!isset($items[$item['id']])) {
                            $items[$item['id']] = [
                                'item'     => new Item($this->getEnvironment(), $item['id']),
                                'item_id'  => $item['id'],
                                'quantity' => $item['count'],
                            ];
                        } else {
                            $items[$item['id']]['quantity'] += $item['count'];
                        }
                    }
                }
                foreach ($items as &$item) {
                    $item['price'] = $item['item']->getPrice();
                }
                $this->deliveryItems = array_values($items);
            }
        }
        return $this->deliveryItems;
    }

    /**
     *
     * @return array
     */
    public function getCurrentBuys()
    {
        if (!isset($this->currentBuys)) {
            $items             = $this->getEnvironment()->getClientVersion2()->apiCommerceTransactionsCurrentBuys();
            $this->currentBuys = $this->prepareItems($items);
        }
        return $this->currentBuys;
    }

    /**
     *
     * @return array
     */
    public function getCurrentSells()
    {
        if (!isset($this->currentSells)) {
            $items              = $this->getEnvironment()->getClientVersion2()->apiCommerceTransactionsCurrentSells();
            $this->currentSells = $this->prepareItems($items);
        }
        return $this->currentSells;
    }

    /**
     *
     * @return array
     */
    public function getHistoryBuys()
    {
        if (!isset($this->historyBuys)) {
            $items             = $this->getEnvironment()->getClientVersion2()->apiCommerceTransactionsHistoryBuys();
            $this->historyBuys = $this->prepareItems($items);
        }
        return $this->historyBuys;
    }

    /**
     *
     * @return array
     */
    public function getHistorySells()
    {
        if (!isset($this->historySells)) {
            $items              = $this->getEnvironment()->getClientVersion2()->apiCommerceTransactionsHistorySells();
            $this->historySells = $this->prepareItems($items);
        }
        return $this->historySells;
    }
}
