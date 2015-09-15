<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\External;

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Model\Character;
use Arnapou\GW2Api\Model\Item;

class GW2SkillsLinkBuilder {

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getRace(Character $character) {
        $map = [
            Character::RACE_HUMAN   => '1',
            Character::RACE_CHARR   => '2',
            Character::RACE_NORN    => '3',
            Character::RACE_ASURA   => '4',
            Character::RACE_SYLVARI => '5',
        ];
        if (isset($map[$character->getRace()])) {
            return $map[$character->getRace()];
        }
        return '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getProfession(Character $character) {
        $map = [
            Character::PROFESSION_ELEMENTALIST => '1',
            Character::PROFESSION_WARRIOR      => '2',
            Character::PROFESSION_RANGER       => '3',
            Character::PROFESSION_NECROMANCER  => '4',
            Character::PROFESSION_GUARDIAN     => '5',
            Character::PROFESSION_THIEF        => '6',
            Character::PROFESSION_ENGINEER     => '7',
            Character::PROFESSION_MESMER       => '8',
            Character::PROFESSION_REVENANT     => '9',
        ];
        if (isset($map[$character->getProfession()])) {
            return $map[$character->getProfession()];
        }
        return '0';
    }

    /**
     * 
     * @param Item $weapon
     * @return string
     */
    protected function getWeapon(Item $weapon = null) {
        $map = [
            Item::SUBTYPE_WEAPON_AXE        => '7',
            Item::SUBTYPE_WEAPON_DAGGER     => '8',
            Item::SUBTYPE_WEAPON_MACE       => '9',
            Item::SUBTYPE_WEAPON_PISTOL     => '10',
            Item::SUBTYPE_WEAPON_SCEPTER    => '11',
            Item::SUBTYPE_WEAPON_SWORD      => '12',
            Item::SUBTYPE_WEAPON_FOCUS      => '13',
            Item::SUBTYPE_WEAPON_SHIELD     => '14',
            Item::SUBTYPE_WEAPON_TORCH      => '15',
            Item::SUBTYPE_WEAPON_WARHORN    => '16',
            Item::SUBTYPE_WEAPON_GREATSWORD => '1',
            Item::SUBTYPE_WEAPON_HAMMER     => '2',
            Item::SUBTYPE_WEAPON_LONGBOW    => '3',
            Item::SUBTYPE_WEAPON_SHORTBOW   => '5',
            Item::SUBTYPE_WEAPON_RIFLE      => '4',
            Item::SUBTYPE_WEAPON_STAFF      => '6',
        ];
        if (empty($weapon)) {
            return '0';
        }
        if (isset($map[$weapon->getSubType()])) {
            return $map[$weapon->getSubType()];
        }
        return '0';
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    protected function getWeapons(Character $character) {
        return $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_A1)) .
            '.' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_A2)) .
            ':' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_B1)) .
            '.' . $this->getWeapon($character->getEquipment(Character::SLOT_WEAPON_B2))
        ;
    }

    /**
     * 
     * @param Character $character
     * @return string
     */
    public function getLink(Character $character) {
        $client   = $character->getClient();
        $lang     = $client->getClientV2()->getLang();
        $cache    = $client->getClientV2()->getRequestManager()->getCache();
        $cacheKey = 'gw2skills-link/' . $lang . '/' . $character->getName();
        if ($cache) {
//            $url = $cache->get($cacheKey);
//            if ($url) {
//                return $url;
//            }
        }

        $data = [
            'bf'   => '0.0',
            'inf'  => '0.0.0.0.0.0.0.0.0.0.0.0:0.0.0.0.0.0:0.0.0.0.0.0',
            'mode' => '1', // PvE
            'p'    => $this->getProfession($character),
            'pet'  => '0.0.0.0',
            'r'    => $this->getRace($character),
            's'    => '0.0.0.0.0',
            'sa'   => '0.0.0.0.0',
            't'    => '',
            'up_a' => '0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0',
            'up_b' => '0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0:0.0.0.0',
            'up_w' => '0.0.0.0.0.0:0.0.0.0.0.0:0.0.0.0.0.0:0.0.0.0.0.0:0.0.0.0.0.0:0.0.0.0.0.0',
            'w'    => $this->getWeapons($character),
        ];

        $curl     = new Curl();
        $curl->setUrl('http://en.gw2skills.net/ajax/buildTcode/');
        $curl->setPost($data);
        $response = $curl->execute();
        $content  = $response->getContent();
        if (preg_match('!^.*?\n(.*?-e)!si', $content, $m)) {
            $url = 'http://' . $lang . '.gw2skills.net/editor/?' . trim($m[1]);
            if ($cache) {
                $cache->set($cacheKey, $url, 900); // 15 min
            }
            return $url;
        }
        return null;
    }

}
