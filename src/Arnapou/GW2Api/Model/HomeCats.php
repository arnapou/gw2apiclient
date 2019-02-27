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

class HomeCats extends AbstractObject implements \IteratorAggregate
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
    protected $homecats;

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
        $this->homecats = [];
        $this->count    = 0;
        $this->total    = 0;

        $env = $this->getEnvironment();
        $map = [];
        foreach ($this->unlocked as $item) {
            if (isset($item['id'])) {
                $map[$item['id']] = $item;
            }
        }

        foreach ($env->getClientVersion2()->apiCats() as $id) {
            $unlocked = isset($map[$id]);
            $homecat  = new HomeCat($env, $id);
            $homecat->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->homecats[] = $homecat;
        }
        uasort($this->homecats, function (HomeCat $homecat1, HomeCat $homecat2) {
            $n1 = $homecat1->getHint();
            $n2 = $homecat2->getHint();
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
    public function getHomeCats()
    {
        if (!isset($this->homecats)) {
            $this->prepareObjects();
        }
        return $this->homecats;
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

    public function getIterator()
    {
        return new \ArrayObject($this->getHomeCats());
    }
}
