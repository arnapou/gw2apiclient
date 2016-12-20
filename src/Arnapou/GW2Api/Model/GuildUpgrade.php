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
 * @method string getDescription()
 * @method string getType()
 * @method string getIcon()
 * @method string getBuildTime()
 * @method string getRequiredLevel()
 * @method string getExperience()
 * @method string getBagMaxItems()
 * @method string getBagMaxCoins()
 */
class GuildUpgrade extends AbstractStoredObject {

    /**
     * 
     * @return array
     */
    public function getPrerequisites() {
        return $this->getData('prerequisites', []);
    }

    /**
     * 
     * @return array
     */
    public function getCosts() {
        return $this->getData('costs', []);
    }

    public function getApiName() {
        return 'guildupgrades';
    }

}
