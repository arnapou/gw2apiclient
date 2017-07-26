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
class HomeNodes extends AbstractObject implements \IteratorAggregate
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
    protected $homenodes;

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
        $this->homenodes = [];
        $this->count     = 0;
        $this->total     = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $id) {
            $map[$id] = $id;
        }

        foreach ($env->getClientVersion2()->apiNodes() as $id) {
            $unlocked = isset($map[$id]);
            $homenode = new HomeNode($env, $id);
            $homenode->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->homenodes[] = $homenode;
        }
        uasort($this->homenodes, function (HomeNode $homenode1, HomeNode $homenode2) {
            $n1 = $homenode1->getId();
            $n2 = $homenode2->getId();
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
    public function getHomeNodes()
    {
        if (!isset($this->homenodes)) {
            $this->prepareObjects();
        }
        return $this->homenodes;
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

    public function getIterator()
    {
        return new \ArrayObject($this->getHomeNodes());
    }
}
