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

class Gliders extends AbstractObject
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
    protected $gliders;

    /**
     *
     * @var int
     */
    protected $count;

    /**
     *
     * @var int
     */
    protected $total;

    protected function setData($data)
    {
        parent::setData($data);

        $this->unlocked = $data['unlocked'] ?? [];
    }

    
    protected function prepareObjects()
    {
        $this->gliders = [];
        $this->count   = 0;
        $this->total   = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $id) {
            $map[$id] = $id;
        }

        foreach ($env->getClientVersion2()->apiGliders() as $id) {
            $unlocked = isset($map[$id]);
            $glider   = new Glider($env, $id);
            $glider->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->gliders[] = $glider;
        }
        uasort($this->gliders, function (Glider $glider1, Glider $glider2) {
            $n1 = $glider1->getOrder();
            $n2 = $glider2->getOrder();
            if ($n1 == $n2) {
                return 0;
            }
            return $n1 > $n2 ? 1 : -1;
        });
    }

    /**
     *
     * @return array
     */
    public function getGliders()
    {
        if (!isset($this->gliders)) {
            $this->prepareObjects();
        }
        return $this->gliders;
    }

    /**
     *
     * @return int
     */
    public function getCount()
    {
        return \count($this->unlocked);
    }

    /**
     *
     * @return int
     */
    public function getTotal()
    {
        if (!isset($this->total)) {
            $this->prepareObjects();
        }
        return $this->total;
    }
}
