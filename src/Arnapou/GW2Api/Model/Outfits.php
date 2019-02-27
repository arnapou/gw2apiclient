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

class Outfits extends AbstractObject
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
    protected $outfits;

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
        $this->outfits = [];
        $this->count   = 0;
        $this->total   = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $id) {
            $map[$id] = $id;
        }

        foreach ($env->getClientVersion2()->apiOutfits() as $id) {
            $unlocked = isset($map[$id]);
            $outfit   = new Outfit($env, $id);
            $outfit->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->outfits[] = $outfit;
        }
        uasort($this->outfits, function (Outfit $outfit1, Outfit $outfit2) {
            $n1 = $outfit1->getOrder();
            $n2 = $outfit2->getOrder();
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
    public function getOutfits()
    {
        if (!isset($this->outfits)) {
            $this->prepareObjects();
        }
        return $this->outfits;
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
