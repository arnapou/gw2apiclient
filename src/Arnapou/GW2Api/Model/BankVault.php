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

class BankVault extends AbstractObject
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
    protected $price;

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['items']) && \is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $this->inventorySlots[] = new InventorySlot($this->getEnvironment(), $item);
            }
        }
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
    public function getPrice()
    {
        if (!isset($this->price)) {
            $this->price = [
                'buy'  => 0,
                'sell' => 0,
            ];
            foreach ($this->inventorySlots as /* @var $item InventorySlot */
                     $item) {
                if ($item && empty($item->getBinding())) {
                    $price               = $item->getPrice();
                    $this->price['buy']  += $price['buy_total'];
                    $this->price['sell'] += $price['sell_total'];
                }
            }
        }
        return $this->price;
    }
}
