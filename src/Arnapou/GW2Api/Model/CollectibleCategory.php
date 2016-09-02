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
 */
class CollectibleCategory extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $items = [];

    /**
     *
     * @var array
     */
    protected $price;

    /**
     *
     * @var Material
     */
    protected $material;

    protected function setData($data) {
        parent::setData($data);

        $env = $this->getEnvironment();
        if (isset($data['id'])) {
            $this->material = new Material($env, $data['id']);
            $ids            = $this->material->getItemIds();

            if (isset($data['items']) && is_array($data['items']) && !empty($ids) && is_array($ids)) {
                $items = array_combine($ids, $ids);
                foreach ($data['items'] as $item) {
                    if (isset($item['id'], $item['count']) && isset($items[$item['id']])) {
                        $items[$item['id']] = new InventorySlot($env, [
                            'id'    => $item['id'],
                            'count' => $item['count'],
                        ]);
                    }
                }
                foreach ($items as $id => $item) {
                    if (!($item instanceof InventorySlot)) {
                        $items[$id] = new InventorySlot($env, [
                            'id'    => $id,
                            'count' => 0,
                        ]);
                    }
                }
                $this->items = array_values($items);
            }
        }
    }

    /**
     * 
     * @return string
     */
    public function getOrder() {
        if ($this->material) {
            return $this->material->getOrder();
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        if ($this->material) {
            return $this->material->getName();
        }
        return null;
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->getData('id');
    }

    /**
     * 
     * @return array
     */
    public function getItems() {
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
