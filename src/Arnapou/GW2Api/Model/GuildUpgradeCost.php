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
 * @method string getType()
 * @method string getCount()
 * @method string getName()
 * @method string getItemId()
 */
class GuildUpgradeCost extends AbstractObject
{

    const TYPE_ITEM        = 'Item';
    const TYPE_COLLECTIBLE = 'Collectible';
    const TYPE_CURRENCY    = 'Currency';

    protected $item;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['item_id'])) {
            $this->item = new InventorySlot($this->getEnvironment(), [
                'id'    => $data['item_id'],
                'count' => isset($data['count']) ? $data['count'] : 1,
            ]);
        }
    }

    /**
     * 
     * @return InventorySlot
     */
    public function getItem()
    {
        return $this->item;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
