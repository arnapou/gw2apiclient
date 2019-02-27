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
 * @doc https://wiki.guildwars2.com/wiki/API:2/files
 *
 * @method string getIcon()
 */
class File extends AbstractStoredObject
{
    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getIcon();
    }

    public function getApiName()
    {
        return 'files';
    }
}
