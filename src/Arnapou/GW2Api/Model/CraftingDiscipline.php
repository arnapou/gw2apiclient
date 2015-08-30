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

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class CraftingDiscipline extends AbstractObject {

    // DISCIPLINES
    const DISCIPLINE_ARMORSMITH    = 'Armorsmith';
    const DISCIPLINE_ARTIFICER     = 'Artificer';
    const DISCIPLINE_CHEF          = 'Chef';
    const DISCIPLINE_HUNTSMAN      = 'Huntsman';
    const DISCIPLINE_JEWELER       = 'Jeweler';
    const DISCIPLINE_LEATHERWORKER = 'Leatherworker';
    const DISCIPLINE_TAILOR        = 'Tailor';
    const DISCIPLINE_WEAPONSMITH   = 'Weaponsmith';

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->data['discipline'];
    }

    /**
     * 
     * @return integer
     */
    public function getRating() {
        return $this->data['rating'];
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
        $files = [
            self::DISCIPLINE_ARMORSMITH    => 'map_crafting_armorsmith',
            self::DISCIPLINE_ARTIFICER     => 'map_crafting_artificer',
            self::DISCIPLINE_CHEF          => 'map_crafting_cook',
            self::DISCIPLINE_HUNTSMAN      => 'map_crafting_huntsman',
            self::DISCIPLINE_JEWELER       => 'map_crafting_jeweler',
            self::DISCIPLINE_LEATHERWORKER => 'map_crafting_leatherworker',
            self::DISCIPLINE_TAILOR        => 'map_crafting_tailor',
            self::DISCIPLINE_WEAPONSMITH   => 'map_crafting_weaponsmith',
        ];
        if (isset($files[$this->getName()])) {
            return $this->apiIcon($files[$this->getName()]);
        }
        return null;
    }

    /**
     * 
     * @return boolean
     */
    public function isActive() {
        return $this->data['active'] ? true : false;
    }

}
