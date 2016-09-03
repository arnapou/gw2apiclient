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
 * @method string getUnlock()
 * @method string getIcon()
 * @method string getOrder()
 * @method string getItemId()
 */
class Mini extends AbstractStoredObject {

    use UnlockTrait;

    protected $item;

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['item_id'])) {
            $this->item = new Item($this->getEnvironment(), $data['item_id']);
        }
    }

    /**
     * 
     * @return Item
     */
    public function getItem() {
        return $this->item;
    }

    public function getApiName() {
        return 'minis';
    }

}
