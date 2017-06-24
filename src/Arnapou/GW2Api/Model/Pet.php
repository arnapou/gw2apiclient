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
 * @method string getIcon()
 */
class Pet extends AbstractStoredObject
{

    use UnlockTrait;

    protected $item;

    public function getApiName()
    {
        return 'pets';
    }
}
