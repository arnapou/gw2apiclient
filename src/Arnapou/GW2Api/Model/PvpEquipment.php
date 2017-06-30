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
class PvpEquipment extends AbstractObject
{

    protected $amulet;
    protected $rune;
    protected $sigils = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['amulet'])) {
            $this->amulet = new PvpAmulet($this->getEnvironment(), $data['amulet']);
        }
        if (isset($data['rune'])) {
            $this->rune = new Item($this->getEnvironment(), $data['rune']);
        }
        if (isset($data['sigils']) && is_array($data['sigils'])) {
            foreach ($data['sigils'] as $id) {
                $this->sigils[] = new Item($this->getEnvironment(), $id);
            }
        }
    }

    /**
     *
     * @return PvpAmulet
     */
    public function getAmulet()
    {
        return $this->amulet;
    }

    /**
     *
     * @return Item
     */
    public function getRune()
    {
        return $this->rune;
    }

    /**
     *
     * @return array
     */
    public function getSigils()
    {
        return $this->sigils;
    }
}
