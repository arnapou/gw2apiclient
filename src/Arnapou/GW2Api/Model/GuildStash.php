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

/**
 *
 * @method integer getId()
 * @method string getSize()
 * @method string getCoins()
 * @method string getNote()
 * @method string getUpgradeId()
 */
class GuildStash extends AbstractObject
{

    /**
     *
     * @var array
     */
    protected $inventorySlots = [];

    /**
     *
     * @var GuildUpgrade
     */
    protected $upgrade;

    /**
     *
     * @var array
     */
    protected $stashprice;

    /**
     *
     * @param array $data
     */
    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['inventory']) && is_array($data['inventory'])) {
            $env = $this->getEnvironment();
            foreach ($data['inventory'] as $item) {
                $this->inventorySlots[] = new InventorySlot($env, $item);
            }
        }

        if (isset($data['upgrade_id'])) {
            $this->upgrade = new GuildUpgrade($this->getEnvironment(), $data['upgrade_id']);
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
     * @return GuildUpgrade
     */
    public function getUpgrade()
    {
        return $this->upgrade;
    }

    /**
     *
     * @return array
     */
    public function getStashPrice()
    {
        if (!isset($this->stashprice)) {
            $this->stashprice = [
                'buy'  => $this->getCoins(),
                'sell' => $this->getCoins(),
            ];
            foreach ($this->inventorySlots as /* @var $item InventorySlot */
                     $item) {
                if ($item && empty($item->getBinding())) {
                    $price                    = $item->getPrice();
                    $this->stashprice['buy']  += $price['buy_total'];
                    $this->stashprice['sell'] += $price['sell_total'];
                }
            }
        }
        return $this->stashprice;
    }
}
