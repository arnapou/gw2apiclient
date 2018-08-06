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
 * @method string getRank()
 */
class GuildMember extends AbstractObject
{
    public function getId()
    {
        return $this->getData('name');
    }

    /**
     *
     * @return string YYYY-MM-DD HH:MM:SS UTC format
     */
    public function getJoined()
    {
        $date = $this->getData('joined');
        return $date ? gmdate('Y-m-d H:i:s', strtotime($date)) : null;
    }

    public function __toString()
    {
        return (string)$this->getName();
    }
}
