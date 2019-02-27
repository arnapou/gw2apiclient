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

class RaidWing extends AbstractObject
{
    protected $events = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['events']) && \is_array($data['events'])) {
            $env = $this->getEnvironment();
            foreach ($data['events'] as $event) {
                if (isset($event['id'])) {
                    $this->events[] = new RaidWingEvent($env, $event);
                }
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
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
     * @return int
     */
    public function getNumber()
    {
        return $this->getData('number');
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
