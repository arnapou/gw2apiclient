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
class Bag extends Item {

    /**
     *
     * @var array
     */
    protected $inventory;

    /**
     *
     * @var integer
     */
    protected $size;

    /**
     *
     * @var array
     */
    protected $inventoryStuff;

    /**
     *
     * @var array
     */
    protected $bagprice;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client, $data['id']);

        $this->size      = $data['size'];
        $this->inventory = [];
        foreach ($data['inventory'] as $item) {
            if (is_array($item) && isset($item['id'])) {
                $this->inventory[] = new InventorySlot($this->client, $item);
            }
            else {
                $this->inventory[] = null;
            }
        }
    }

    /**
     * 
     * @return string
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * 
     * @return array
     */
    public function getInventory() {
        return $this->inventory;
    }

    /**
     * 
     * @return array
     */
    public function getInventoryStuff() {
        if (!isset($this->inventoryStuff)) {
            $allowedRarities = [self::RARITY_LEGENDARY, self::RARITY_ASCENDED, self::RARITY_EXOTIC];
            $allowedTypes    = [self::TYPE_ARMOR, self::TYPE_BACK, self::TYPE_WEAPON, self::TYPE_TRINKET];

            $this->inventoryStuff = [];
            foreach ($this->inventory as /* @var $item InventorySlot */ $item) {
                if (empty($item) ||
                    !in_array($item->getRarity(), $allowedRarities) ||
                    !in_array($item->getType(), $allowedTypes)
                ) {
                    continue;
                }
                $this->inventoryStuff[$item->getSubType()][] = $item;
            }
        }
        return $this->inventoryStuff;
    }

    /**
     * 
     * @return array
     */
    public function getBagPrice() {
        if (!isset($this->bagprice)) {
            $this->bagprice = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->getInventory() as /* @var $item InventorySlot */ $item) {
                if ($item) {
                    $price = $item->getPrice();
                    $this->bagprice['buy'] += $price['buy_total'];
                    $this->bagprice['sell'] += $price['sell_total'];
                }
            }
        }
        return $this->bagprice;
    }

}
