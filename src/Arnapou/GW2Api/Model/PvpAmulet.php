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
 * @method string getIcon()
 * @method string getAttributes()
 */
class PvpAmulet extends AbstractStoredObject
{
    public function getApiName()
    {
        return 'pvpamulets';
    }
}
