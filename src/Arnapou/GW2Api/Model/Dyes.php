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

class Dyes extends AbstractObject
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
    protected $colors;

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
        $this->colors = [];
        $this->count  = 0;
        $this->total  = 0;

        $env             = $this->getEnvironment();
        $flippedUnlocked = array_flip($this->unlocked);
        foreach ($env->getClientVersion2()->apiColors() as $id) {
            $unlocked = isset($flippedUnlocked[$id]);
            $color    = new Color($env, $id);
            $color->setUnlocked($unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->colors[] = $color;
        }

        uasort($this->colors, function (Color $color1, Color $color2) {
            return strcmp($color1->getName(), $color2->getName());
        });
    }

    /**
     *
     * @return array
     */
    public function getColors()
    {
        if (!isset($this->colors)) {
            $this->prepareObjects();
        }
        return $this->colors;
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
