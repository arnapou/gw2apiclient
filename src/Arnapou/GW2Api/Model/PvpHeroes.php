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

class PvpHeroes extends AbstractObject
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
    protected $heroes;

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
        $this->total        = 0;

        $env = $this->getEnvironment();

        foreach ($env->getClientVersion2()->apiPvpHeroes() as $id) {
            $hero = new PvpHero($env, $id);
            $hero->setUnlocked($this->unlocked);
            $this->heroes[] = $hero;
        }
        foreach ($this->heroes as /* @var $hero PvpHero */
                 $hero) {
            $this->total += count($hero->getSkins());
        }
        uasort($this->mailcarriers, function (PvpHero $hero1, PvpHero $hero2) {
            $n1 = $hero1->getName();
            $n2 = $hero2->getName();
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
    public function getHeroes()
    {
        if (!isset($this->heroes)) {
            $this->prepareObjects();
        }
        return $this->heroes;
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
