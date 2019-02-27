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

class Raid extends AbstractStoredObject
{
    protected $wings = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['wings']) && \is_array($data['wings'])) {
            $env = $this->getEnvironment();
            $i   = 0;
            foreach ($data['wings'] as $wing) {
                if (isset($wing['id'])) {
                    $i++;
                    $wing['number'] = $i;
                    $this->wings[]  = new RaidWing($env, $wing);
                }
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getWings()
    {
        $this->checkLoadData();
        return $this->wings;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return \Arnapou\GW2Api\id_to_name($this->getId());
    }

    public function getApiName()
    {
        return 'raids';
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
