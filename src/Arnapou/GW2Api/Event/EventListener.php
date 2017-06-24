<?php
/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Arnapou\GW2Api\Event;

class EventListener
{

    protected $events = array();

    /**
     * 
     */
    public function __construct()
    {
        
    }

    /**
     * 
     * @param string $eventName
     * @param mixed $callable
     * @param type $append
     */
    public function bind($eventName, $callable, $append = true)
    {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = array();
        }
        if ($append) {
            array_push($this->events[$eventName], $callable);
        } else {
            array_unshift($this->events[$eventName], $callable);
        }
    }

    /**
     * 
     * @param string $eventName
     * @param Event $event
     */
    public function trigger($eventName, $event = null)
    {
        if (isset($this->events[$eventName])) {
            if ($event === null) {
                foreach ($this->events[$eventName] as $callable) {
                    call_user_func($callable);
                }
            } elseif ($event instanceof Event) {
                foreach ($this->events[$eventName] as $callable) {
                    call_user_func($callable, $event);
                }
            } else {
                throw new \BadMethodCallException('Argument $event should be a valid Arnapou\GW2Api\Event\Event object');
            }
        }
    }
}
