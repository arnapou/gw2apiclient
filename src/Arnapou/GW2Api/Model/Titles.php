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
class Titles extends AbstractObject
{

    /**
     *
     * @var array
     */
    protected $unlocked;

    /**
     *
     * @var array
     */
    protected $titles;

    /**
     *
     * @var integer
     */
    protected $count;

    /**
     *
     * @var integer
     */
    protected $total;

    protected function setData($data)
    {
        parent::setData($data);

        $this->unlocked = isset($data['unlocked']) ? $data['unlocked'] : [];
    }

    /**
     *
     */
    protected function prepareObjects()
    {
        $this->titles = [];
        $this->count  = 0;
        $this->total  = 0;

        $env             = $this->getEnvironment();
        $flippedUnlocked = array_flip($this->unlocked);
        foreach ($env->getClientVersion2()->apiTitles() as $id) {
            $unlocked = isset($flippedUnlocked[$id]);
            $title    = new Title($env, $id);
            $title->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->titles[$id] = $title;
        }
        uasort($this->titles, function (Title $title1, Title $title2) {
            return strcmp($title1->getName(), $title2->getName());
        });
    }

    /**
     *
     * @return array
     */
    public function getTitles()
    {
        if (!isset($this->titles)) {
            $this->prepareObjects();
        }
        return $this->titles;
    }

    /**
     *
     * @return integer
     */
    public function getCount()
    {
        return count($this->unlocked);
    }

    /**
     *
     * @return integer
     */
    public function getTotal()
    {
        if (!isset($this->total)) {
            $this->prepareObjects();
        }
        return $this->total;
    }
}
