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
trait UnlockTrait
{

    protected $__unlocked = false;

    /**
     * 
     * @return boolean
     */
    public function isUnlocked()
    {
        return $this->__unlocked;
    }

    /**
     * 
     * @param boolean $bool
     */
    public function setUnlocked($bool)
    {
        $this->__unlocked = $bool ? true : false;
    }
}
