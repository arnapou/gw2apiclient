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
class Minis extends AbstractObject
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
    protected $minis;

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
        $this->minis = [];
        $this->count = 0;
        $this->total = 0;

        $env             = $this->getEnvironment();
        $flippedUnlocked = array_flip($this->unlocked);
        foreach ($env->getClientVersion2()->apiMinis() as $id) {
            $unlocked      = isset($flippedUnlocked[$id]);
            $mini          = new Mini($env, $id);
            $mini->setUnlocked($unlocked);
            $this->count   += $unlocked ? 1 : 0;
            $this->total++;
            $this->minis[] = $mini;
        }
        uasort($this->minis, function(Mini $mini1, Mini $mini2) {
            $n1 = $mini1->getOrder();
            $n2 = $mini2->getOrder();
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
    public function getMinis()
    {
        if (!isset($this->minis)) {
            $this->prepareObjects();
        }
        return $this->minis;
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
