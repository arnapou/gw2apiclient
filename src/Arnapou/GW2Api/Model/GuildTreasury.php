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
class GuildTreasury extends AbstractObject
{

    protected $item;
    protected $neededBy   = [];
    protected $totalCount = 0;

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();

        if (isset($data['item_id'], $data['count'])) {
            $this->item = new InventorySlot($env, [
                'id'    => $data['item_id'],
                'count' => $data['count'],
            ]);
        }

        if (isset($data['needed_by']) && is_array($data['needed_by'])) {
            foreach ($data['needed_by'] as $item) {
                if (isset($item['upgrade_id'])) {
                    $item['upgrade'] = new GuildUpgrade($env, $item['upgrade_id']);
                }
                if (isset($item['count'])) {
                    $this->totalCount += $item['count'];
                }
                $this->neededBy[] = $item;
            }
        }
    }

    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->item ? $this->item->getId() : null;
    }

    /**
     * 
     * @return integer
     */
    public function getCount()
    {
        return $this->item ? $this->item->getCount() : 0;
    }

    /**
     * 
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * 
     * @return array
     */
    public function getNeededBy()
    {
        return $this->neededBy;
    }

    /**
     * 
     * @return InventorySlot
     */
    public function getItem()
    {
        return $this->item;
    }
}
