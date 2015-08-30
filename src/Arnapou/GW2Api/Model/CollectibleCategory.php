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
class CollectibleCategory extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $items;

    /**
     *
     * @var array
     */
    protected $price;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->data['name'];
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * 
     * @return array
     */
    public function getItems() {
        if (!isset($this->items)) {
            $this->items = [];
            foreach ($this->data['items'] as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $this->items[] = new InventorySlot($this->client, $item);
                }
                else {
                    $this->items[] = null;
                }
            }
        }
        return $this->items;
    }

    /**
     * 
     * @return array
     */
    public function getPrice() {
        if (!isset($this->price)) {
            $this->price = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->getItems() as /* @var $item InventorySlot */ $item) {
                if ($item) {
                    $price = $item->getPrice();
                    $this->price['buy'] += $price['buy_total'];
                    $this->price['sell'] += $price['sell_total'];
                }
            }
        }
        return $this->price;
    }

}
