<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api;

use Arnapou\GW2Api\Exception\JsonException;
use Arnapou\GW2Api\Model\Item;

/**
 * 
 * @param Item $item
 * @return boolean
 */
function is_two_handed_weapon($item) {
    if ($item) {
        return in_array($item->getSubType(), [
                Item::SUBTYPE_WEAPON_GREATSWORD, Item::SUBTYPE_WEAPON_HAMMER,
                Item::SUBTYPE_WEAPON_LONGBOW, Item::SUBTYPE_WEAPON_RIFLE,
                Item::SUBTYPE_WEAPON_SHORTBOW, Item::SUBTYPE_WEAPON_STAFF
            ]) ? true : false;
    }
    return false;
}

/**
 * 
 * @param array $array
 * @return boolean
 */
function is_associative_array($array) {
    $values = array_values($array);
    $diff   = array_diff_key($values, $array);
    return empty($diff) ? false : true;
}

/**
 * 
 * @param string $url
 * @param string|array $params
 * @return string
 */
function url_append($url, $params) {
    if (empty($params)) {
        return $url;
    }
    foreach ($params as $key => $param) {
        if (is_array($param)) {
            $params[$key] = implode(',', $param);
        }
    }
    $url .= (strpos($url, '?') === false) ? '?' : '&';
    if (is_array($params)) {
        $url .= http_build_query($params);
    }
    else {
        $url .= (string) $params;
    }
    return $url;
}

/**
 * 
 * @param string $json
 * @return array
 */
function json_decode($json) {
    $json = trim($json);
    if ($json === '' || ($json[0] !== '{' && $json[0] !== '[' && $json[0] !== '"')) {
        throw new JsonException('Json not valid : ' . $json);
    }
    $array         = \json_decode($json, true);
    $jsonLastError = json_last_error();
    if ($jsonLastError !== JSON_ERROR_NONE) {
        $errors = array(
            JSON_ERROR_DEPTH            => 'Max depth reached.',
            JSON_ERROR_STATE_MISMATCH   => 'Mismatch modes or underflow.',
            JSON_ERROR_CTRL_CHAR        => 'Character control error.',
            JSON_ERROR_SYNTAX           => 'Malformed JSON.',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, probably charset problem.',
            JSON_ERROR_RECURSION        => 'Recursion detected.',
            JSON_ERROR_INF_OR_NAN       => 'Inf or NaN',
            JSON_ERROR_UNSUPPORTED_TYPE => 'Unsupported type.',
        );
        throw new JsonException('Json error : ' . (isset($errors[$jsonLastError]) ? $errors[$jsonLastError] : 'Unknown error'));
    }
    return $array;
}

/**
 * 
 * @param array $attributes
 * @return string
 */
function attributes_to_statname($attributes) {
    $flatten = '';
    if (is_array($attributes)) {
        if (count($attributes) >= 7) {
            return 'Celestial';
        }
        uasort($attributes, function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return $a < $b ? 1 : -1;
        });
        $flatten = implode('/', array_keys($attributes));
    }
    $statNames = [
        'Power/Precision/CritDamage'                           => "Berserker's",
        'Power/CritDamage/Precision'                           => "Berserker's",
        'Power/CritDamage/Precision/Vitality'                  => "Berserker's + Valkyrie",
        'Power/Healing/Precision'                              => "Zealot's",
        'Power/Precision/Healing'                              => "Zealot's",
        'Power/Toughness/Vitality'                             => "Soldier's",
        'Power/Vitality/Toughness'                             => "Soldier's",
        'Power/CritDamage/Vitality'                            => "Valkyrie",
        'Power/Vitality/CritDamage'                            => "Valkyrie",
        'Power/Toughness/Healing'                              => "Forsaken",
        'Power/Healing/Toughness'                              => "Forsaken",
        'Precision/Toughness/Power'                            => "Captain's",
        'Precision/Power/Toughness'                            => "Captain's",
        'Precision/ConditionDamage/Power'                      => "Rampager's",
        'Precision/Power/ConditionDamage'                      => "Rampager's",
        'Precision/CritDamage/Power'                           => "Assassin's",
        'Precision/Power/CritDamage'                           => "Assassin's",
        'Toughness/Precision/Power'                            => "Knight's",
        'Toughness/Power/Precision'                            => "Knight's",
        'Toughness/Power/CritDamage'                           => "Cavalier's",
        'Toughness/CritDamage/Power'                           => "Cavalier's",
        'Toughness/Healing/Vitality'                           => "Nomad's",
        'Toughness/Vitality/Healing'                           => "Nomad's",
        'Toughness/Healing/ConditionDamage'                    => "Settler's",
        'Toughness/ConditionDamage/Healing'                    => "Settler's",
        'Toughness/Healing'                                    => "Giver's",
        'Healing/Toughness'                                    => "Giver's",
        'Vitality/Toughness/Power'                             => "Sentinel's",
        'Vitality/Power/Toughness'                             => "Sentinel's",
        'Vitality/Healing/ConditionDamage'                     => "Shaman's",
        'Vitality/ConditionDamage/Healing'                     => "Shaman's",
        'Vitality/Healing/Power'                               => "of the shaman",
        'Vitality/Power/Healing'                               => "of the shaman",
        'ConditionDamage/Precision/Power'                      => "Sinister",
        'ConditionDamage/Power/Precision'                      => "Sinister",
        'ConditionDamage/Vitality/Power'                       => "Carrion",
        'ConditionDamage/Power/Vitality'                       => "Carrion",
        'ConditionDamage/Toughness/Precision'                  => "Rabid",
        'ConditionDamage/Precision/Toughness'                  => "Rabid",
        'ConditionDamage/Toughness/Precision/Healing'          => "Rabid + Apothecary's",
        'ConditionDamage/Vitality/Toughness'                   => "Dire",
        'ConditionDamage/Toughness/Vitality'                   => "Dire",
        'ConditionDamage/Toughness/Vitality/Precision'         => "Dire + Rabid",
        'ConditionDamage/Healing/Toughness'                    => "Apostate's",
        'ConditionDamage/Toughness/Healing'                    => "Apostate's",
        'Healing/Toughness/Power'                              => "Cleric's",
        'Healing/Power/Toughness'                              => "Cleric's",
        'Healing/Vitality/Precision'                           => "Magi's",
        'Healing/Precision/Vitality'                           => "Magi's",
        'Healing/ConditionDamage/Toughness'                    => "Apothecary's",
        'Healing/Toughness/ConditionDamage'                    => "Apothecary's",
        // HoT stats
        'Power/Precision/BoonDuration/Toughness'               => "Commander",
        'Power/Precision/Toughness/BoonDuration'               => "Commander",
        'Precision/Power/BoonDuration/Toughness'               => "Commander",
        'Precision/Power/Toughness/BoonDuration'               => "Commander",
        'Power/Vitality/Toughness/BoonDuration'                => "Wanderer",
        'Power/Vitality/BoonDuration/Toughness'                => "Wanderer",
        'Vitality/Power/Toughness/BoonDuration'                => "Wanderer",
        'Vitality/Power/BoonDuration/Toughness'                => "Wanderer",
        'Power/Precision/Vitality/CritDamage'                  => "Marauder",
        'Power/Precision/CritDamage/Vitality'                  => "Marauder",
        'Precision/Power/Vitality/CritDamage'                  => "Marauder",
        'Precision/Power/CritDamage/Vitality'                  => "Marauder",
        'Power/Toughness/CritDamage/Healing'                   => "Crusader",
        'Power/Toughness/Healing/CritDamage'                   => "Crusader",
        'Toughness/Power/CritDamage/Healing'                   => "Crusader",
        'Toughness/Power/Healing/CritDamage'                   => "Crusader",
        'Toughness/ConditionDamage/Vitality/ConditionDuration' => "Trailblazer",
        'Toughness/ConditionDamage/ConditionDuration/Vitality' => "Trailblazer",
        'ConditionDamage/Toughness/Vitality/ConditionDuration' => "Trailblazer",
        'ConditionDamage/Toughness/ConditionDuration/Vitality' => "Trailblazer",
        'Toughness/Healing/Vitality/BoonDuration'              => "Minstrel",
        'Toughness/Healing/BoonDuration/Vitality'              => "Minstrel",
        'Healing/Toughness/Vitality/BoonDuration'              => "Minstrel",
        'Healing/Toughness/BoonDuration/Vitality'              => "Minstrel",
        'Toughness/Power/ConditionDuration/BoonDuration'       => "Vigilant",
        'Toughness/Power/BoonDuration/ConditionDuration'       => "Vigilant",
        'Power/Toughness/ConditionDuration/BoonDuration'       => "Vigilant",
        'Power/Toughness/BoonDuration/ConditionDuration'       => "Vigilant",
        'Power/ConditionDamage/ConditionDuration/Precision'    => "Viper's",
        'Power/ConditionDamage/Precision/ConditionDuration'    => "Viper's",
        'ConditionDamage/Power/ConditionDuration/Precision'    => "Viper's",
        'ConditionDamage/Power/Precision/ConditionDuration'    => "Viper's",
    ];
    if ($flatten && isset($statNames[$flatten])) {
        return $statNames[$flatten];
    }
    return '';
}
