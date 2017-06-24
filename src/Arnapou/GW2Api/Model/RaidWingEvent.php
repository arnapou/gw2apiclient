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
 */
class RaidWingEvent extends AbstractObject
{

    use UnlockTrait;

    const TYPE_CHECKPOINT = 'Checkpoint';
    const TYPE_BOSS       = 'Boss';

    /**
     * 
     * @return string
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * 
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return \Arnapou\GW2Api\id_to_name($this->getId());
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
