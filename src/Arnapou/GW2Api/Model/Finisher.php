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
 * @method string getName()
 * @method string getOrder()
 * @method string getIcon()
 * @method string getUnlockDetails()
 */
class Finisher extends AbstractStoredObject
{

    use UnlockTrait;

    protected $quantity    = 0;
    protected $isPermanent = false;
    protected $unlockItems = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['unlock_items']) && is_array($data['unlock_items'])) {
            foreach ($data['unlock_items'] as $id) {
                $this->unlockItems[] = new Item($this->getEnvironment(), $id);
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getUnlockItem($index)
    {
        return isset($this->unlockItems[$index]) ? $this->unlockItems[$index] : null;
    }

    /**
     *
     * @return array
     */
    public function getUnlockItems()
    {
        return $this->unlockItems;
    }

    /**
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @param integer $nb
     */
    public function setQuantity($nb)
    {
        $this->quantity = $nb;
    }

    /**
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->isPermanent;
    }

    /**
     *
     * @param boolean $bool
     */
    public function setPermanent($bool)
    {
        $this->isPermanent = $bool ? true : false;
    }

    public function getApiName()
    {
        return 'finishers';
    }
}
