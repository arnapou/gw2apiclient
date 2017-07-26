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
class GuildUpgrade extends AbstractStoredObject
{

    protected $prerequisites = [];
    protected $costs         = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['prerequisites']) && is_array($data['prerequisites'])) {
            $env = $this->getEnvironment();
            foreach ($data['prerequisites'] as $id) {
                $this->prerequisites[] = new GuildUpgrade($env, $id);
            }
        }

        if (isset($data['costs']) && is_array($data['costs'])) {
            $env = $this->getEnvironment();
            foreach ($data['costs'] as $item) {
                $this->costs[] = new GuildUpgradeCost($env, $item);
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getPrerequisites()
    {
        return $this->prerequisites;
    }

    /**
     *
     * @return array
     */
    public function getCosts()
    {
        return $this->costs;
    }

    public function getApiName()
    {
        return 'guildupgrades';
    }

    public function __toString()
    {
        return $this->getName();
    }
}
