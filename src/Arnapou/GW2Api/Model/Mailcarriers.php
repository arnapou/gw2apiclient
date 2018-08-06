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

class Mailcarriers extends AbstractObject
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
    protected $mailcarriers;

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

        $this->unlocked = isset($data['unlocked']) ? $data['unlocked'] : [];
    }

    
    protected function prepareObjects()
    {
        $this->mailcarriers = [];
        $this->count        = 0;
        $this->total        = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $id) {
            $map[$id] = $id;
        }

        foreach ($env->getClientVersion2()->apiMailcarriers() as $id) {
            $unlocked    = isset($map[$id]);
            $mailcarrier = new Mailcarrier($env, $id);
            $mailcarrier->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->mailcarriers[] = $mailcarrier;
        }
        uasort($this->mailcarriers, function (Mailcarrier $mailcarrier1, Mailcarrier $mailcarrier2) {
            $n1 = $mailcarrier1->getOrder();
            $n2 = $mailcarrier2->getOrder();
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
    public function getMailcarriers()
    {
        if (!isset($this->mailcarriers)) {
            $this->prepareObjects();
        }
        return $this->mailcarriers;
    }

    /**
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->unlocked);
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
