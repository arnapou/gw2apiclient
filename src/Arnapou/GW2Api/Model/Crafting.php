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
 * @method string getDiscipline()
 * @method string getRating()
 * @method string isActive()
 */
class Crafting extends AbstractObject
{
    // DISCIPLINES
    const DISCIPLINE_ARMORSMITH    = 'Armorsmith';
    const DISCIPLINE_ARTIFICER     = 'Artificer';
    const DISCIPLINE_CHEF          = 'Chef';
    const DISCIPLINE_HUNTSMAN      = 'Huntsman';
    const DISCIPLINE_JEWELER       = 'Jeweler';
    const DISCIPLINE_LEATHERWORKER = 'Leatherworker';
    const DISCIPLINE_TAILOR        = 'Tailor';
    const DISCIPLINE_WEAPONSMITH   = 'Weaponsmith';
    const DISCIPLINE_SCRIBE        = 'Scribe';

    /**
     *
     * @var array
     */
    protected $files = [
        self::DISCIPLINE_ARMORSMITH    => 'map_crafting_armorsmith',
        self::DISCIPLINE_ARTIFICER     => 'map_crafting_artificer',
        self::DISCIPLINE_CHEF          => 'map_crafting_cook',
        self::DISCIPLINE_HUNTSMAN      => 'map_crafting_huntsman',
        self::DISCIPLINE_JEWELER       => 'map_crafting_jeweler',
        self::DISCIPLINE_LEATHERWORKER => 'map_crafting_leatherworker',
        self::DISCIPLINE_TAILOR        => 'map_crafting_tailor',
        self::DISCIPLINE_WEAPONSMITH   => 'map_crafting_weaponsmith',
//        self::DISCIPLINE_SCRIBE        => 'map_crafting_scribe',
    ];

    /**
     *
     * @var File
     */
    protected $icon = null;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['discipline'], $this->files[$data['discipline']])) {
            $this->icon = new File($this->getEnvironment(), $this->files[$data['discipline']]);
        }
    }

    /**
     *
     * @return File
     */
    public function getIconFile()
    {
        return $this->icon;
    }

    public function __toString()
    {
        return $this->getDiscipline();
    }
}
