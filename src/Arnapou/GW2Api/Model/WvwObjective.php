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
 * @method string getType()
 * @method string getSectorId()
 * @method string getMapId()
 * @method string getMapType()
 * @method string getCoord()
 * @method string getLabelCoord()
 * @method string getMarker()
 */
class WvwObjective extends AbstractStoredObject {

    const TYPE_CAMP      = 'Camp';
    const TYPE_CASTLE    = 'Castle';
    const TYPE_KEEP      = 'Keep';
    const TYPE_MERCENARY = 'Mercenary';
    const TYPE_TOWER     = 'Tower';
    const TYPE_RUINS     = 'Ruins';
    const TYPE_RESOURCE  = 'Resource';
    const TYPE_GENERIC   = 'Generic';
    const TYPE_SPAWN     = 'Spawn';

    public function getApiName() {
        return 'wvwobjectives';
    }

}
