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
class Finishers extends AbstractObject
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
    protected $finishers;

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
        $this->finishers = [];
        $this->count     = 0;
        $this->total     = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $item) {
            $map[$item['id']] = $item;
        }

        foreach ($env->getClientVersion2()->apiFinishers() as $id) {
            $unlocked = isset($map[$id]);
            $finisher = new Finisher($env, $id);
            $finisher->setUnlocked($unlocked);
            if ($unlocked && isset($map[$id]['quantity'])) {
                $finisher->setQuantity($map[$id]['quantity']);
            }
            if ($unlocked && isset($map[$id]['permanent'])) {
                $finisher->setPermanent($map[$id]['permanent']);
            }
            $this->count       += $unlocked ? 1 : 0;
            $this->total++;
            $this->finishers[] = $finisher;
        }
        uasort($this->finishers, function(Finisher $finisher1, Finisher $finisher2) {
            $n1 = $finisher1->getOrder();
            $n2 = $finisher2->getOrder();
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
    public function getFinishers()
    {
        if (!isset($this->finishers)) {
            $this->prepareObjects();
        }
        return $this->finishers;
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
