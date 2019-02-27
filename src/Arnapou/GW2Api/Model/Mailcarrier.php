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
 */
class Mailcarrier extends AbstractStoredObject
{
    use UnlockTrait;

    protected $unlockItems = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['unlock_items']) && \is_array($data['unlock_items'])) {
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
        $this->checkLoadData();
        return isset($this->unlockItems[$index]) ? $this->unlockItems[$index] : null;
    }

    /**
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->getData('flags', []);
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return \in_array($flag, (array)$this->getFlags());
    }

    /**
     *
     * @return array
     */
    public function getUnlockItems()
    {
        return $this->unlockItems;
    }

    public function getApiName()
    {
        return 'mailcarriers';
    }
}
