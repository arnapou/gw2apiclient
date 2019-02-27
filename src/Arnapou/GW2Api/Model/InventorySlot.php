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
 * @method getId()
 * @method getSlot()
 * @method getBinding()
 * @method getBoundTo()
 */
class InventorySlot extends AbstractObject
{
    const BINDING_ACCOUNT   = 'Account';
    const BINDING_CHARACTER = 'Character';

    /**
     *
     * @var array
     */
    protected $infusions = [];

    /**
     *
     * @var array
     */
    protected $upgrades = [];

    /**
     *
     * @var int
     */
    protected $count = null;

    /**
     *
     * @var int
     */
    protected $charges = null;

    /**
     *
     * @var Skin
     */
    protected $skin;

    /**
     *
     * @var Item
     */
    protected $item;

    /**
     *
     * @var ItemStat
     */
    protected $itemStat;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['upgrades']) && \is_array($data['upgrades'])) {
            foreach ($data['upgrades'] as $id) {
                $this->upgrades[] = new Item($this->getEnvironment(), $id);
            }
        }
        if (isset($data['infusions']) && \is_array($data['infusions'])) {
            foreach ($data['infusions'] as $id) {
                $this->infusions[] = new Item($this->getEnvironment(), $id);
            }
        }
        if (isset($data['skin'])) {
            $this->skin = new Skin($this->getEnvironment(), $data['skin']);
        }
        if (isset($data['id'])) {
            $this->item = new Item($this->getEnvironment(), $data['id']);
        }
        if (isset($data['charges'])) {
            $this->charges = (int)$data['charges'];
        }
        if (isset($data['count'])) {
            $this->count = (int)$data['count'];
        } else {
            $this->count = 1;
        }
        if (isset($data['stats'], $data['stats']['id'])) {
            $this->itemStat = new ItemStat($this->getEnvironment(), $data['stats']['id']);
        }
    }

    
    public function getChatLink()
    {
        if ($this->getId()) {
            $skin     = $this->skin ? $this->skin->getId() : 0;
            $upgrade1 = isset($this->upgrades[0]) ? $this->upgrades[0]->getId() : 0;
            $upgrade2 = isset($this->upgrades[1]) ? $this->upgrades[1]->getId() : 0;
            return \Arnapou\GW2Api\chatlink_item($this->getId(), $skin, $upgrade1, $upgrade2);
        }
        return '';
    }

    /**
     *
     * @return array [buy: x, sell: y]
     */
    public function getPrice()
    {
        if ($this->item && empty($this->getBinding()) && empty($this->getBoundTo())) {
            $n                   = $this->getCount();
            $price               = $this->item->getPrice();
            $price['buy_total']  = $price['buy'] * $n;
            $price['sell_total'] = $price['sell'] * $n;
            return $price;
        }
        return [
            'buy'        => null,
            'sell'       => null,
            'buy_total'  => null,
            'sell_total' => null,
        ];
    }

    public function __call($name, $arguments)
    {
        $val = parent::__call($name, $arguments);
        if ($val === null && empty($arguments) && $this->item) {
            return $this->item->$name();
        }
        return $val;
    }

    /**
     *
     * @return Item[]
     */
    public function getInfusions()
    {
        return $this->infusions;
    }

    /**
     *
     * @return int
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     *
     * @return Item[]
     */
    public function getUpgrades()
    {
        return $this->upgrades;
    }

    /**
     *
     * @return Skin
     */
    public function getSkin()
    {
        return $this->skin;
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
     * @return ItemStat
     */
    public function getItemStat()
    {
        if ($this->itemStat) {
            return $this->itemStat;
        } elseif ($this->item) {
            return $this->getItem()->getItemStat();
        }
        return null;
    }

    /**
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData(['stats', 'attributes'], []);
        if (empty($attributes)) {
            $attributes = $this->getItem()->getAttributes();
        }
        return $attributes;
    }

    /**
     *
     * @return int
     */
    public function getAgonyResistance()
    {
        $ar = $this->item ? $this->item->getAgonyResistance() : 0;
        foreach ($this->getUpgrades() as $item) {
            if ($item) {
                $ar += $item->getAgonyResistance();
            }
        }
        foreach ($this->getInfusions() as $item) {
            if ($item) {
                $ar += $item->getAgonyResistance();
            }
        }
        return (int)$ar;
    }

    /**
     *
     * @return string
     */
    public function getStatName()
    {
        if ($this->itemStat && !$this->itemStat->isEmpty()) {
            return $this->itemStat->getStatName();
        }
        $name = \Arnapou\GW2Api\attributes_to_statname($this->getAttributes());
        if ($name) {
            return $name;
        }
        if ($this->item) {
            return $this->getItem()->getStatName();
        }
        return null;
    }
}
