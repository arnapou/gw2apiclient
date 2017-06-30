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
 * @doc https://wiki.guildwars2.com/wiki/API:2/maps
 *
 * @method string getName()
 * @method string getMinLevel()
 * @method string getMaxLevel()
 * @method string getDefaultFloor()
 * @method string getFloors()
 * @method string getRegionId()
 * @method string getRegionName()
 * @method string getContinentId()
 * @method string getContinentName()
 * @method string getMapRect()
 * @method string getContinentRect()
 */
class Map extends AbstractStoredObject
{

    public function getApiName()
    {
        return 'maps';
    }

    public function __toString()
    {
        return $this->getName();
    }
}
