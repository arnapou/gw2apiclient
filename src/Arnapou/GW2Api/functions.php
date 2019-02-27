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
use MongoDB\Database as MongoDatabase;

/**
 *
 * @param string $id
 * @return string
 */
function id_to_name($id)
{
    return ucfirst(str_replace('_', ' ', $id));
}

/**
 * https://wiki.guildwars2.com/wiki/Chat_link_format
 *
 * @param int $item
 * @param int $skin
 * @param int $upgrade1
 * @param int $upgrade2
 * @return string
 */
function chatlink_item($item, $skin = 0, $upgrade1 = 0, $upgrade2 = 0, $quantity = 1)
{
    if (empty($item)) {
        return null;
    }
    if ($quantity > 250) {
        $quantity = 250;
    }
    $hex = function ($id) {
        $h = dechex((int)$id);
        $n = 6 - \strlen($h);
        $s = ($n > 0 ? str_repeat('0', $n) : '') . $h;
        return $s[4] . $s[5] . $s[2] . $s[3] . $s[0] . $s[1];
    };
    $s   = '02' . substr($hex($quantity), 0, 2) . $hex($item);
    // 0x00 – Default item
    if (empty($skin) && empty($upgrade1) && empty($upgrade2)) {
        $s .= '00';
    } // 0x40 – 1 upgrade component
    elseif (empty($skin) && !empty($upgrade1) && empty($upgrade2)) {
        $s .= '40' . $hex($upgrade1) . '00';
    } // 0x60 – 2 upgrade components
    elseif (empty($skin) && !empty($upgrade1) && !empty($upgrade2)) {
        $s .= '60' . $hex($upgrade1) . '00' . $hex($upgrade2) . '00';
    } // 0x80 – Skinned
    elseif (!empty($skin) && empty($upgrade1) && empty($upgrade2)) {
        $s .= '80' . $hex($skin) . '00';
    } // 0xC0 – Skinned + 1 upgrade component
    elseif (!empty($skin) && !empty($upgrade1) && empty($upgrade2)) {
        $s .= 'C0' . $hex($skin) . '00' . $hex($upgrade1) . '00';
    } // 0xE0 – Skinned + 2 upgrade components
    elseif (!empty($skin) && !empty($upgrade1) && !empty($upgrade2)) {
        $s .= 'E0' . $hex($skin) . '00' . $hex($upgrade1) . '00' . $hex($upgrade2) . '00';
    }
    return '[&' . base64_encode(pack('H*', $s)) . ']';
}

/**
 *
 * @param MongoDatabase $mongoDB
 * @return string
 */
function get_mongo_database_error(MongoDatabase $mongoDB)
{
    if (!method_exists($mongoDB, '__debugInfo')) {
        return "Magic method '__debugInfo' not found.";
    }
    $debug = $mongoDB->__debugInfo();
    if (!isset($debug['typeMap'])) {
        return "Debug sub key 'typeMap' not found.";
    }
    if (!isset($debug['typeMap']['root']) || strtolower($debug['typeMap']['root']) !== 'array') {
        return "Mongo database option 'typeMap' with subkey 'root' should be equal to 'array'.";
    }
    if (!isset($debug['typeMap']['document']) || strtolower($debug['typeMap']['document']) !== 'array') {
        return "Mongo database option 'typeMap' with subkey 'document' should be equal to 'array'.";
    }
    return null;
}

/**
 *
 * @param Item $item
 * @return bool
 */
function is_two_handed_weapon($item)
{
    if ($item) {
        return \in_array($item->getSubType(), [
            Item::SUBTYPE_WEAPON_GREATSWORD,
            Item::SUBTYPE_WEAPON_HAMMER,
            Item::SUBTYPE_WEAPON_LONGBOW,
            Item::SUBTYPE_WEAPON_RIFLE,
            Item::SUBTYPE_WEAPON_SHORTBOW,
            Item::SUBTYPE_WEAPON_STAFF,
        ]) ? true : false;
    }
    return false;
}

/**
 *
 * @param array $array
 * @return bool
 */
function is_associative_array($array)
{
    $values = array_values($array);
    $diff   = array_diff_key($values, $array);
    return empty($diff) ? false : true;
}

/**
 *
 * @param string       $url
 * @param string|array $params
 * @return string
 */
function url_append($url, $params)
{
    if (empty($params)) {
        return $url;
    }
    foreach ($params as $key => $param) {
        if (\is_array($param)) {
            $params[$key] = implode(',', $param);
        }
    }
    $url .= (strpos($url, '?') === false) ? '?' : '&';
    if (\is_array($params)) {
        $url .= http_build_query($params);
    } else {
        $url .= (string)$params;
    }
    return $url;
}

/**
 *
 * @param string $json
 * @return array
 * @throws JsonException
 */
function json_decode($json)
{
    $json = trim($json);
    if ($json === '' || ($json[0] !== '{' && $json[0] !== '[' && $json[0] !== '"')) {
        throw new JsonException('Json not valid : ' . $json);
    }
    $array         = \json_decode($json, true);
    $jsonLastError = json_last_error();
    if ($jsonLastError !== JSON_ERROR_NONE) {
        $errors = [
            JSON_ERROR_DEPTH            => 'Max depth reached.',
            JSON_ERROR_STATE_MISMATCH   => 'Mismatch modes or underflow.',
            JSON_ERROR_CTRL_CHAR        => 'Character control error.',
            JSON_ERROR_SYNTAX           => 'Malformed JSON.',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, probably charset problem.',
            JSON_ERROR_RECURSION        => 'Recursion detected.',
            JSON_ERROR_INF_OR_NAN       => 'Inf or NaN',
            JSON_ERROR_UNSUPPORTED_TYPE => 'Unsupported type.',
        ];
        throw new JsonException('Json error : ' . ($errors[$jsonLastError] ?? 'Unknown error'));
    }
    return $array;
}

/**
 *
 * @param array $attributes
 * @return string
 */
function attributes_to_statname($attributes)
{
    $flatten = '';
    if (\is_array($attributes)) {
        if (\count($attributes) >= 7) {
            return 'Celestial';
        }
        $attrs = [];
        if (is_associative_array($attributes)) {
            foreach ($attributes as $attr => $value) {
                $attrs[] = ['name' => $attr, 'value' => $value];
            }
        } else {
            foreach ($attributes as $attribute) {
                $attr       = $attribute['attribute'] ?? '';
                $multiplier = $attribute['multiplier'] ?? 0;
                $value      = $attribute['value'] ?? 0;
                $attrs[]    = ['name' => $attr, 'value' => $value ?: $multiplier];
            }
        }
        usort($attrs, function ($a, $b) {
            $ret = $a['value'] <=> $b['value'];
            if ($ret == 0) {
                return strtolower($a['name']) <=> strtolower($b['name']);
            }
            return -$ret;
        });
        $flatten = implode('/', array_column($attrs, 'name'));
    }
    $statNames = [
        'Power/CritDamage/Precision'                           => "Berserker's",
        'Power/CritDamage/Precision/Vitality'                  => "Berserker's + Valkyrie",
        'Power/Healing/Precision'                              => "Zealot's",
        'Power/Toughness/Vitality'                             => "Soldier's",
        'Power/CritDamage/Vitality'                            => 'Valkyrie',
        'Power/Healing/Toughness'                              => 'Forsaken',
        'Precision/Power/Toughness'                            => "Captain's",
        'Precision/ConditionDamage/Power'                      => "Rampager's",
        'Precision/CritDamage/Power'                           => "Assassin's",
        'Toughness/Power/Precision'                            => "Knight's",
        'Toughness/CritDamage/Power'                           => "Cavalier's",
        'Toughness/Healing/Vitality'                           => "Nomad's",
        'Toughness/ConditionDamage/Healing'                    => "Settler's",
        'Toughness/Healing/BoonDuration'                       => "Giver's",
        'ConditionDuration/Precision/Vitality'                 => "Giver's",
        'Vitality/Power/Toughness'                             => "Sentinel's",
        'Vitality/ConditionDamage/Healing'                     => "Shaman's",
        'Vitality/Healing/Power'                               => 'of the shaman',
        'ConditionDamage/Power/Precision'                      => 'Sinister',
        'ConditionDamage/Power/Vitality'                       => 'Carrion',
        'ConditionDamage/Precision/Toughness'                  => 'Rabid',
        'ConditionDamage/Toughness/Precision/Healing'          => "Rabid + Apothecary's",
        'ConditionDamage/Toughness/Vitality'                   => 'Dire',
        'ConditionDamage/Toughness/Vitality/Precision'         => 'Dire + Rabid',
        'ConditionDamage/Healing/Toughness'                    => "Apostate's",
        'Healing/Power/Toughness'                              => "Cleric's",
        'Healing/Precision/Vitality'                           => "Magi's",
        'Healing/ConditionDamage/Toughness'                    => "Apothecary's",
        // HoT stats
        'Power/Precision/BoonDuration/Toughness'               => 'Commander',
        'Power/Vitality/BoonDuration/Toughness'                => 'Wanderer',
        'Power/Precision/CritDamage/Vitality'                  => 'Marauder',
        'Power/Toughness/CritDamage/Healing'                   => 'Crusader',
        'ConditionDamage/Toughness/ConditionDuration/Vitality' => 'Trailblazer',
        'Healing/Toughness/BoonDuration/Vitality'              => 'Minstrel',
        'Power/Toughness/BoonDuration/ConditionDuration'       => 'Vigilant',
        'ConditionDamage/Power/ConditionDuration/Precision'    => "Viper's",
        // HoT stats - LS
        'ConditionDamage/Precision/BoonDuration/Healing'       => 'Seraph',
        // PoF stats
        'Healing/Power/ConditionDamage/Precision'              => "Marshal's",
        'Power/BoonDuration/Healing'                           => "Harrier's",
        'ConditionDamage/Power/CritDamage/Precision'           => 'Grieving',
        // PoF stats - LS
        'Vitality/ConditionDuration/Precision'                 => 'Bringer',
        'ConditionDamage/Vitality/BoonDuration/Healing'        => 'Plaguedoctor',
    ];
    if ($flatten && isset($statNames[$flatten])) {
        return $statNames[$flatten];
    }
    return '';
}
