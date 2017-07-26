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
 * @method string getId()
 * @method string getName()
 * @method string getIcon()
 */
class PvpHeroSkin extends AbstractObject
{

    use UnlockTrait;

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
    public function isDefault()
    {
        return $this->getData('default', false) ? true : false;
    }

    /**
     *
     * @return array
     */
    public function getUnlockItems()
    {
        return $this->unlockItems;
    }
}
