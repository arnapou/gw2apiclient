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
 * @method string getDescription()
 */
class Glider extends AbstractStoredObject
{
    use UnlockTrait;

    protected $unlockItems = [];
    protected $defaultDyes = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['unlock_items']) && \is_array($data['unlock_items'])) {
            foreach ($data['unlock_items'] as $id) {
                $this->unlockItems[] = new Item($this->getEnvironment(), $id);
            }
        }

        if (isset($data['default_dyes']) && \is_array($data['default_dyes'])) {
            foreach ($data['default_dyes'] as $id) {
                $this->defaultDyes[] = new Color($this->getEnvironment(), $id);
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
    public function getUnlockItems()
    {
        return $this->unlockItems;
    }

    /**
     *
     * @return array
     */
    public function getDefaultDye($index)
    {
        return isset($this->defaultDyes[$index]) ? $this->defaultDyes[$index] : null;
    }

    /**
     *
     * @return array
     */
    public function getDefaultDyes()
    {
        return $this->defaultDyes;
    }

    public function getApiName()
    {
        return 'gliders';
    }
}
