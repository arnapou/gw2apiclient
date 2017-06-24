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

/**
 *
 * @method integer getId()
 * @method integer getSize()
 */
class Bag extends InventorySlot
{

    /**
     *
     * @var array
     */
    protected $inventorySlots = [];

    /**
     *
     * @var array
     */
    protected $item = null;

    /**
     *
     * @var array
     */
    protected $bagprice;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['id'])) {
            $this->item = new Item($this->getEnvironment(), $data['id']);
        }
        if (isset($data['inventory']) && is_array($data['inventory'])) {
            foreach ($data['inventory'] as $item) {
                $this->inventorySlots[] = new InventorySlot($this->getEnvironment(), $item);
            }
        }
    }

    /**
     * 
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * 
     * @return array
     */
    public function getInventorySlots()
    {
        return $this->inventorySlots;
    }

    /**
     * 
     * @return array
     */
    public function getBagPrice()
    {
        if (!isset($this->bagprice)) {
            $this->bagprice = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->inventorySlots as /* @var $item InventorySlot */ $item) {
                if ($item && empty($item->getBinding())) {
                    $price                  = $item->getPrice();
                    $this->bagprice['buy']  += $price['buy_total'];
                    $this->bagprice['sell'] += $price['sell_total'];
                }
            }
        }
        return $this->bagprice;
    }
}
