<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\External\GW2Skills;

use Arnapou\GW2Api\Cache\CacheInterface;
use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Core\CurlResponse;
use Arnapou\GW2Api\Exception\RequestException;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Model\Character;

/* * ********************************************************************** *
 *                                                                          *
 *                               IMPORTANT                                  *
 *                                                                          *
 * ************************************************************************ *
 *                                                                          *
 *          In order to use this class without any problem, your IP         *
 *                  should be whitelisted by gw2skills.net                  *
 *                                                                          *
 *                      contact: info@gw2skills.net                         *
 *                                                                          *
 * ************************************************************************ */

class Client {

    /**
     *
     * @var array
     */
    protected $modes = [
        'pvp' => 1,
        'pve' => 2,
        'wvw' => 3,
    ];

    /**
     *
     * @var string
     */
    protected $remoteUri = 'http://api.gw2skills.net/v1/';

    /**
     *
     * @var array
     */
    protected $files;

    /**
     *
     * @var integer
     */
    protected $revision;

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var array
     */
    protected $map;

    /**
     *
     * @var integer
     */
    protected $revisionCheckDelay = 900; // 15 min
    /**
     *
     * @var integer
     */
    protected $requestTimeout     = 5; // seconds
    /**
     *
     * @var string
     */
    protected $requestUserAgent   = 'GW2 Api client';

    /**
     *
     * @var array
     */
    protected $statsClassMap = [
        'berserker'            => "Berserker's",
        'soldier'              => "Soldier's",
        'cavalier'             => "Cavalier's",
        'rabid'                => "Rabid",
        'magi'                 => "Magi's",
        'dire'                 => "Dire",
        'apothecary'           => "Apothecary's",
        'assassin'             => "Assassin's",
        'carrion'              => "Carrion",
        'cleric'               => "Cleric's",
        'knight'               => "Knight's",
        'rampager'             => "Rampager's",
        'sentinel'             => "Sentinel's",
        'settler'              => "Settler's",
        'shaman'               => "Shaman's",
        'valkyrie'             => "Valkyrie",
        'sinister'             => "Sinister",
        'nomad'                => "Nomad's",
        'celestial'            => "Celestial",
        'giver'                => "Giver's",
        'zealot'               => "Zealot's",
        'berserker + valkyrie' => "Berserker's + Valkyrie",
        'captain'              => "Captain's",
        'dire + rabid'         => "Dire + Rabid",
        'rabid + apothecary'   => "Rabid + Apothecary's",
    ];

    /**
     * 
     */
    public function __construct() {
        $this->files = [
            'revision' => __DIR__ . '/config/revision.php',
            'alldata'  => __DIR__ . '/config/alldata.php',
            'gw2names' => __DIR__ . '/config/gw2names.php',
            'mapped'   => __DIR__ . '/config/mapped.php',
            'unmapped' => __DIR__ . '/config/unmapped.php',
        ];
        $this->getRevision();
    }

    /**
     * 
     * @param string $filename
     * @param mixed $data
     */
    protected function exportToFile($filename, $data) {
        file_put_contents($filename, '<?php return ' . var_export($data, true) . ";\n", LOCK_EX);
    }

    /**
     * 
     * @return integer
     */
    public function getRevision() {
        if (!isset($this->revision)) {
            $filename = $this->files['revision'];
            try {
                if (is_file($filename)) {
                    $this->revision = include($filename);
                }

                if (!is_file($filename) || time() - filemtime($filename) > $this->revisionCheckDelay) {
                    try {
                        $revision = $this->request('revision/');
                    }
                    catch (\Exception $ex) {
                        // if the request fail, we skip it totally : silent
                        // it happens if the requestor has not the right to access gw2 skills api
                        @touch($filename);
                        return;
                    }
                    if (!ctype_digit($revision)) {
                        throw new RequestException('GW2 Skills revision is not a valid integer');
                    }
                    if ($revision != $this->revision) {
                        $this->reloadData();
                        $this->exportToFile($filename, (int) $revision);
                    }
                    else {
                        @touch($filename);
                    }
                    $this->revision = $revision;
                }
            }
            catch (\Exception $ex) {
                if (!is_file($filename)) {
                    throw $ex;
                }
            }
        }
        return $this->revision;
    }

    /**
     * 
     */
    public function buildMap() {
        $gw2names = include($this->files['gw2names']);
        $alldata  = include($this->files['alldata']);

        $mapped   = [
            'professions'     => [],
            'races'           => [],
            'weapons'         => [],
            'specializations' => [],
            'traits'          => [],
            'upgrades'        => [],
            'items'           => [],
        ];
        $unmapped = [];

        // professions
        foreach ($alldata['professions'] as $item) {
            $name                         = strtolower($item['name']);
            $mapped['professions'][$name] = $item['id'];
        }
        asort($mapped['professions']);

        // races
        foreach ($alldata['races'] as $item) {
            $name                   = strtolower($item['name']);
            $mapped['races'][$name] = $item['id'];
        }
        asort($mapped['races']);

        // weapons
        foreach ($alldata['weapons'] as $item) {
            $name                     = strtolower($item['name']);
            $name                     = str_replace('harpoon gun', 'harpoon', $name);
            $name                     = str_replace('spear', 'speargun', $name);
            $mapped['weapons'][$name] = $item['id'];
        }
        asort($mapped['weapons']);

        // specializations
        $specializations = [];
        foreach ($alldata['specializations'] as $item) {
            $profession = strtolower($item['profession']);
            $name       = strtolower($item['name']);
            if (isset($gw2names['specializations'][$profession][$name])) {
                $gw2id                             = $gw2names['specializations'][$profession][$name];
                $mapped['specializations'][$gw2id] = $item['id'];
                $specializations[$item['id']]      = $gw2id;
            }
            else {
                $unmapped['specializations'][] = $item;
            }
        }
        ksort($mapped['specializations']);

        // traits
        foreach ($alldata['traits'] as $item) {
            $name = strtolower($item['name']);
            if (isset($specializations[$item['specialization_id']], $gw2names['traits'][$specializations[$item['specialization_id']]][$name])) {
                $gw2id                    = $gw2names['traits'][$specializations[$item['specialization_id']]][$name];
                $mapped['traits'][$gw2id] = $item['id'];
            }
            else {
                $unmapped['traits'][] = $item;
            }
        }
        ksort($mapped['traits']);

        // upgrades
        foreach ($alldata['upgrades'] as $item) {
            $name       = strtolower($item['name']);
            $name       = str_replace('of flame legion', 'of the flame legion', $name);
            $rarity     = strtolower($item['rarity']);
            $rarity     = str_replace('common', 'fine', $rarity);
            $is_profile = $item['is_profile'];
            $pvx        = (int) $item['pvx'];
            $found      = false;
            foreach ($this->modes as $mode => $int) {
                if ($pvx & $int) {
                    if (isset($gw2names['upgrades'][$rarity][$name])) {
                        $gw2id                    = $gw2names['upgrades'][$rarity][$name];
                        $key                      = $mode . '.' . $rarity . '.' . $gw2id;
                        $found                    = true;
                        $mapped['upgrades'][$key] = ($is_profile ? 1 : 0) . '.' . $item['id'];
                    }
                }
            }
            if (!$found) {
                $unmapped['upgrades'][] = $item;
            }
        }
        ksort($mapped['upgrades']);

        // stats
        foreach ($alldata['items'] as $item) {
            $name        = strtolower($item['name']);
            $rarity      = strtolower($item['rarity']);
            $is_profile  = $item['is_profile'];
            $pvx         = (int) $item['pvx'];
            $found       = false;
            $stats_class = isset($item['stats_class']) ? strtolower($item['stats_class']) : '';
            $group       = !empty($item['group']) ? strtolower($item['group']) : '';
            $type        = !empty($item['type']) ? strtolower($item['type']) : '';
            $type        = str_replace('earring', 'accessory', $type);
            foreach ($this->modes as $mode => $int) {
                if ($pvx & $int) {
                    if (isset($this->statsClassMap[$stats_class])) {
                        $stat       = strtolower($this->statsClassMap[$stats_class]);
                        $gwtypes    = [];
                        $gwsubtypes = [];
                        if ($group == 'weapon' && empty($type)) {
                            $gwtypes    = [Item::TYPE_WEAPON];
                            $gwsubtypes = [
                                Item::SUBTYPE_WEAPON_AXE,
                                Item::SUBTYPE_WEAPON_DAGGER,
                                Item::SUBTYPE_WEAPON_FOCUS,
                                Item::SUBTYPE_WEAPON_GREATSWORD,
                                Item::SUBTYPE_WEAPON_HAMMER,
                                Item::SUBTYPE_WEAPON_HARPOON,
                                Item::SUBTYPE_WEAPON_LONGBOW,
                                Item::SUBTYPE_WEAPON_MACE,
                                Item::SUBTYPE_WEAPON_PISTOL,
                                Item::SUBTYPE_WEAPON_RIFLE,
                                Item::SUBTYPE_WEAPON_SCEPTER,
                                Item::SUBTYPE_WEAPON_SHIELD,
                                Item::SUBTYPE_WEAPON_SPEARGUN,
                                Item::SUBTYPE_WEAPON_SHORTBOW,
                                Item::SUBTYPE_WEAPON_STAFF,
                                Item::SUBTYPE_WEAPON_SWORD,
                                Item::SUBTYPE_WEAPON_TORCH,
                                Item::SUBTYPE_WEAPON_TRIDENT,
                                Item::SUBTYPE_WEAPON_WARHORN,
                            ];
                        }
                        elseif ($group == 'armor' && empty($type)) {
                            $gwtypes    = [Item::TYPE_ARMOR];
                            $gwsubtypes = [
                                Item::SUBTYPE_ARMOR_BOOTS,
                                Item::SUBTYPE_ARMOR_COAT,
                                Item::SUBTYPE_ARMOR_GLOVES,
                                Item::SUBTYPE_ARMOR_HELM,
                                Item::SUBTYPE_ARMOR_HELM_AQUATIC,
                                Item::SUBTYPE_ARMOR_LEGGINGS,
                                Item::SUBTYPE_ARMOR_SHOULDERS,
                            ];
                        }
                        elseif ($group == 'trinket' && empty($type)) {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [
                                Item::SUBTYPE_TRINKET_ACCESSORY,
                                Item::SUBTYPE_TRINKET_AMULET,
                                Item::SUBTYPE_TRINKET_RING,
                            ];
                        }
                        elseif (empty($group) && $type == 'back') {
                            $gwtypes    = [Item::TYPE_BACK];
                            $gwsubtypes = [''];
                        }
                        elseif (empty($group) && $type == 'accessory') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_ACCESSORY];
                        }
                        elseif (empty($group) && $type == 'ring') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_RING];
                        }
                        elseif (empty($group) && $type == 'amulet') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_AMULET];
                        }
                        else {
                            $gwtypes    = [$group];
                            $gwsubtypes = [$type];
                        }
                        foreach ($gwtypes as $gwtype) {
                            $gwtype = strtolower($gwtype);
                            foreach ($gwsubtypes as $gwsubtype) {
                                $gwsubtype             = strtolower($gwsubtype);
                                $key                   = $mode . '.' . $rarity . '.' . $gwtype . '.' . $gwsubtype . '.' . $stat;
                                $found                 = true;
                                $mapped['items'][$key] = ($is_profile ? 1 : 0) . '.' . $item['id'];
                            }
                        }
                    }
                }
            }
            if (!$found) {
                $unmapped['items'][] = $item;
            }
        }
        ksort($mapped['items']);

        $this->exportToFile($this->files['mapped'], $mapped);
        $this->exportToFile($this->files['unmapped'], $unmapped);
    }

    /**
     * 
     * Note that this method can only work if you have GW2Tools project loaded
     * 
     */
    public function buildGw2Names() {
        if (class_exists('Arnapou\GW2Tools\MongoCache')) {
            $cache = \Arnapou\GW2Tools\MongoCache::getInstance(false);

            // upgrades
            $upgrades   = [];
            $collection = $cache->getMongoCollection('en_items');
            foreach ($collection->find(['value.type' => 'UpgradeComponent']) as $row) {
                if (isset($row['value']['name'])) {
                    $group                   = strtolower($row['value']['rarity']);
                    $name                    = strtolower($row['value']['name']);
                    $name                    = str_replace('of rata sum', 'of the rata sum', $name);
                    $upgrades[$group][$name] = $row['value']['id'];
                }
            }
            ksort($upgrades);
            foreach ($upgrades as &$submap) {
                ksort($submap);
            }

            // specializations
            $specializations = [];
            $collection      = $cache->getMongoCollection('en_specializations');
            foreach ($collection->find() as $row) {
                if (isset($row['value']['name'])) {
                    $group                          = strtolower($row['value']['profession']);
                    $name                           = strtolower($row['value']['name']);
                    $specializations[$group][$name] = $row['value']['id'];
                }
            }
            ksort($specializations);
            foreach ($specializations as &$submap) {
                ksort($submap);
            }

            // traits
            $traits     = [];
            $collection = $cache->getMongoCollection('en_traits');
            foreach ($collection->find() as $row) {
                if (isset($row['value']['name'])) {
                    $group                 = strtolower($row['value']['specialization']);
                    $name                  = strtolower($row['value']['name']);
                    $traits[$group][$name] = $row['value']['id'];
                }
            }
            ksort($traits);
            foreach ($traits as &$submap) {
                ksort($submap);
            }

            $filename = $this->files['gw2names'];
            $this->exportToFile($filename, [
                'upgrades'        => $upgrades,
                'specializations' => $specializations,
                'traits'          => $traits,
            ]);
            return true;
        }
        return false;
    }

    /**
     * 
     * @param $key
     * @return array
     */
    public function getMap($key = null) {
        if (!isset($this->map)) {
            $filename = $this->files['mapped'];
            if (!is_file($filename)) {
                throw new RequestException('GW2 Skills mapped file cannot be found');
            }
            $this->map = include($filename);
        }
        if ($key !== null) {
            return isset($this->map[$key]) ? $this->map[$key] : [];
        }
        return $this->map;
    }

    /**
     * 
     * @return array
     */
    public function getData() {
        if (!isset($this->data)) {
            $filename = $this->files['alldata'];
            if (!is_file($filename)) {
                throw new RequestException('GW2 Skills data file cannot be found');
            }
            $this->data = include($filename);
        }
        return $this->data;
    }

    /**
     * 
     */
    protected function reloadData() {
        try {
            $data = $this->request('all/');
        }
        catch (\Exception $ex) {
            // if the request fail, we skip it totally : silent
            // it happens if the requestor has not the right to access gw2 skills api
            return;
        }
        if (!is_array($data)) {
            throw new RequestException('GW2 Skills data is not a valid array');
        }
        $filename = $this->files['alldata'];
        $this->exportToFile($filename, $data);

        $this->buildGw2Names();
        $this->buildMap();
    }

    /**
     * 
     * @return array
     */
    protected function request($uri) {
        $url = $this->remoteUri . $uri;

        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setUserAgent($this->requestUserAgent);
        $curl->setTimeout($this->requestTimeout);
        $curl->setGet();

        $response = $curl->execute();

        if ($response->getErrorCode()) {
            throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
        }

        if ($response->getInfoHttpCode() !== 200) {
            throw new RequestException('HTTP Code ' . $response->getInfoHttpCode() . '.');
        }

        $content = $response->getContent();
        if ($content !== '' && $content[0] === '{') {
            return \Arnapou\GW2Api\json_decode($content);
        }
        return $content;
    }

}
