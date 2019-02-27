<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Skills;

use Arnapou\GW2Api\Core\Curl;
use Arnapou\GW2Api\Environment;
use Arnapou\GW2Api\Exception\RequestException;
use Arnapou\GW2Api\Model\Item;
use Arnapou\GW2Api\Storage\MongoStorage;

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

class Client
{
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
     * @var int
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
     * @var int
     */
    protected $revisionCheckDelay = 900; // 15 min
    /**
     *
     * @var int
     */
    protected $requestTimeout = 5; // seconds
    /**
     *
     * @var string
     */
    protected $requestUserAgent = 'GW2 Api client';

    /**
     *
     * @var Environment
     */
    protected $environment;

    /**
     *
     * @var array
     */
    protected $statsClassMap = [
        'berserker'            => "Berserker's",
        'soldier'              => "Soldier's",
        'cavalier'             => "Cavalier's",
        'rabid'                => 'Rabid',
        'magi'                 => "Magi's",
        'dire'                 => 'Dire',
        'apothecary'           => "Apothecary's",
        'assassin'             => "Assassin's",
        'carrion'              => 'Carrion',
        'cleric'               => "Cleric's",
        'knight'               => "Knight's",
        'rampager'             => "Rampager's",
        'sentinel'             => "Sentinel's",
        'settler'              => "Settler's",
        'shaman'               => "Shaman's",
        'valkyrie'             => 'Valkyrie',
        'sinister'             => 'Sinister',
        'nomad'                => "Nomad's",
        'celestial'            => 'Celestial',
        'giver'                => "Giver's",
        'zealot'               => "Zealot's",
        'berserker + valkyrie' => "Berserker's + Valkyrie",
        'captain'              => "Captain's",
        'dire + rabid'         => 'Dire + Rabid',
        'rabid + apothecary'   => "Rabid + Apothecary's",
        // HoT
        'viper'                => "Viper's",
        'commander'            => 'Commander',
        'wanderer'             => 'Wanderer',
        'marauder'             => 'Marauder',
        'crusader'             => 'Crusader',
        'trailblazer'          => 'Trailblazer',
        'minstrel'             => 'Minstrel',
        'vigilant'             => 'Vigilant',
        // HoT stats - Episode 4
        'seraph'               => 'Seraph',
        // PoF stats
        'marshal'              => "Marshal's",
        'harrier'              => "Harrier's",
        'grieving'             => 'Grieving',
    ];

    /**
     *
     * @param Environment $env
     */
    public function __construct(Environment $env)
    {
        $this->environment = $env;
        $this->files       = [
            'revision'   => __DIR__ . '/config/revision.php',
            'alldata'    => __DIR__ . '/config/alldata.php',
            'gw2names'   => __DIR__ . '/config/gw2names.php',
            'mapped'     => __DIR__ . '/config/mapped.php',
            'unmapped'   => __DIR__ . '/config/unmapped.php',
            'fixednames' => __DIR__ . '/config/fixednames.php',
        ];
        $this->getRevision();
    }

    /**
     *
     * @param string $filename
     * @param mixed  $data
     */
    protected function exportToFile($filename, $data)
    {
        file_put_contents($filename, '<?php return ' . var_export($data, true) . ";\n", LOCK_EX);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getRevision()
    {
        if (!isset($this->revision)) {
            $filename = $this->files['revision'];
            try {
                if (is_file($filename)) {
                    $this->revision = include($filename);
                }

                if (!is_file($filename) || time() - filemtime($filename) > $this->revisionCheckDelay) {
                    try {
                        $revision = $this->request('revision/');
                    } catch (\Exception $ex) {
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
                        $this->exportToFile($filename, (int)$revision);
                    } else {
                        @touch($filename);
                    }
                    $this->revision = $revision;
                }
            } catch (\Exception $ex) {
                if (!is_file($filename)) {
                    throw $ex;
                }
            }
        }
        return $this->revision;
    }

    
    public function buildMap()
    {
        $gw2names   = include($this->files['gw2names']);
        $alldata    = include($this->files['alldata']);
        $fixednames = include($this->files['fixednames']);

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
            $name                     = isset($fixednames['weapons']) ? strtr($name, $fixednames['weapons']) : $name;
            $mapped['weapons'][$name] = $item['id'];
        }
        asort($mapped['weapons']);

        // specializations
        $specializations = [];
        foreach ($alldata['specializations'] as $item) {
            $profession = strtolower($item['profession']);
            $name       = strtolower($item['name']);
            $name       = isset($fixednames['specializations']) ? strtr($name, $fixednames['specializations']) : $name;
            if (isset($gw2names['specializations'][$profession][$name])) {
                $gw2id                             = $gw2names['specializations'][$profession][$name];
                $mapped['specializations'][$gw2id] = $item['id'];
                $specializations[$item['id']]      = $gw2id;
            } else {
                $unmapped['specializations'][] = $item;
            }
        }
        ksort($mapped['specializations']);

        // traits
        foreach ($alldata['traits'] as $item) {
            $name = strtolower($item['name']);
            $name = isset($fixednames['traits']) ? strtr($name, $fixednames['traits']) : $name;
            if (isset($specializations[$item['specialization_id']], $gw2names['traits'][$specializations[$item['specialization_id']]][$name])) {
                $gw2id                    = $gw2names['traits'][$specializations[$item['specialization_id']]][$name];
                $mapped['traits'][$gw2id] = $item['id'];
            } else {
                $unmapped['traits'][] = $item;
            }
        }
        ksort($mapped['traits']);

        // pets
        foreach ($alldata['pets'] as $item) {
            $name = strtolower($item['name']);
            $name = isset($fixednames['pets']) ? strtr($name, $fixednames['pets']) : $name;
            if (isset($gw2names['pets'][$name])) {
                $gw2id                  = $gw2names['pets'][$name];
                $mapped['pets'][$gw2id] = $item['id'];
            } else {
                $unmapped['pets'][] = $item;
            }
        }
        ksort($mapped['pets']);

        // buffs
        $fallbackbuffmap = [];
        foreach ($alldata['buffs'] as $item) {
            $name  = strtolower($item['name']);
            $name = isset($fixednames['buffs']) ? strtr($name, $fixednames['buffs']) : $name;
            $pvx   = (int)$item['pvx'];
            $found = false;
            foreach ($this->modes as $mode => $int) {
                if ($pvx & $int) {
                    if (isset($gw2names['buffs'][$name])) {
                        $gw2id                 = $gw2names['buffs'][$name];
                        $key                   = $mode . '.' . $gw2id;
                        $found                 = true;
                        $mapped['buffs'][$key] = $item['id'];
                    }
                }
            }
            if (!$found) {
                $unmapped['buffs'][] = $item;
            } elseif (isset(
                $gw2names['buffstats'],
                $gw2names['buffstats']['byname'],
                $gw2names['buffstats']['byname'][$name]
            )) {
                $buffstat = $gw2names['buffstats']['byname'][$name];
                if ($buffstat) {
                    foreach ($gw2names['buffstats']['bystat'][$buffstat] as $fallbackname) {
                        foreach ($this->modes as $mode => $int) {
                            if ($pvx & $int) {
                                if (isset($gw2names['buffs'][$fallbackname])) {
                                    $gw2id = $gw2names['buffs'][$fallbackname];
                                    $key = $mode . '.' . $gw2id;
                                    $fallbackbuffmap[$key] = $item['id'];
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($fallbackbuffmap as $key => $id) {
            if (!isset($mapped['buffs'][$key])) {
                $mapped['buffs'][$key] = $id;
            }
        }
        ksort($mapped['buffs']);

        // skills
        foreach ($alldata['skills'] as $item) {
            $name       = strtolower($item['name']);
            $name       = isset($fixednames['skills']) ? strtr($name, $fixednames['skills']) : $name;
            $profession = strtolower($item['profession']);
            $found      = false;
            if (isset($gw2names['skills'][$profession], $gw2names['skills'][$profession][$name])) {
                foreach ($gw2names['skills'][$profession][$name] as $gw2id) {
                    $mapped['skills'][$gw2id] = $item['id'];
                }
                $found = true;
            } elseif (empty($profession)) {
                $professions = array_keys($mapped['professions']);
                foreach ($professions as $profession) {
                    if (isset($gw2names['skills'][$profession], $gw2names['skills'][$profession][$name])) {
                        foreach ($gw2names['skills'][$profession][$name] as $gw2id) {
                            $mapped['skills'][$gw2id] = $item['id'];
                        }
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                $unmapped['skills'][] = $item;
            }
        }
        ksort($mapped['skills']);

        // upgrades
        foreach ($alldata['upgrades'] as $item) {
            $name       = strtolower($item['name']);
            $name       = isset($fixednames['upgrades']) ? strtr($name, $fixednames['upgrades']) : $name;
            $pvpname    = isset($item['pvp_name']) ? str_replace(' (pvp)', '', strtolower($item['pvp_name'])) : '';
            $rarity     = strtolower($item['rarity']);
            $rarity     = str_replace('common', 'fine', $rarity);
            $is_profile = $item['is_profile'];
            $pvx        = (int)$item['pvx'];
            $found      = false;
            foreach ($this->modes as $mode => $int) {
                if ($pvx & $int) {
                    if (isset($gw2names['upgrades'][$rarity][$name])) {
                        $gw2id                    = $gw2names['upgrades'][$rarity][$name];
                        $key                      = $mode . '.' . $rarity . '.' . $gw2id;
                        $found                    = true;
                        $mapped['upgrades'][$key] = ($is_profile ? 1 : 0) . '.' . $item['id'];
                    }
                    if ($mode == 'pvp' && $pvpname && isset($gw2names['upgrades'][$rarity][$pvpname])) {
                        $gw2id                    = $gw2names['upgrades'][$rarity][$pvpname];
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
            $name        = isset($fixednames['items']) ? strtr($name, $fixednames['items']) : $name;
            $rarity      = strtolower($item['rarity']);
            $is_profile  = $item['is_profile'];
            $pvx         = (int)$item['pvx'];
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
                        } elseif ($group == 'armor' && empty($type)) {
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
                        } elseif ($group == 'trinket' && empty($type)) {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [
                                Item::SUBTYPE_TRINKET_ACCESSORY,
                                Item::SUBTYPE_TRINKET_AMULET,
                                Item::SUBTYPE_TRINKET_RING,
                            ];
                        } elseif (empty($group) && $type == 'back') {
                            $gwtypes    = [Item::TYPE_BACK];
                            $gwsubtypes = [''];
                        } elseif (empty($group) && $type == 'accessory') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_ACCESSORY];
                        } elseif (empty($group) && $type == 'ring') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_RING];
                        } elseif (empty($group) && $type == 'amulet') {
                            $gwtypes    = [Item::TYPE_TRINKET];
                            $gwsubtypes = [Item::SUBTYPE_TRINKET_AMULET];
                        } else {
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
                    if ($mode == 'pvp' && $type == 'amulet' && $rarity == 'exotic') {
                        $name = str_replace("'s", '', $name);
                        if (isset($gw2names['pvp_items'][$name])) {
                            $gw2id                       = $gw2names['pvp_items'][$name];
                            $mapped['pvp_items'][$gw2id] = $item['id'];
                            $found                       = true;
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
     * @param $data
     * @return null|string
     */
    protected function getBuffStatKey($data)
    {
        if (isset($data['details'], $data['details']['description'])) {
            $lines = [];
            foreach (explode("\n", $data['details']['description']) as $line) {
                if (\stripos($line, ' Karma') !== false ||
                    \stripos($line, ' Experience') !== false ||
                    \stripos($line, ' Magic Find') !== false ||
                    \stripos($line, ' Gold from Monsters') !== false
                ) {
                    continue;
                }
                $lines[] = trim($line);
            }
            sort($lines);
            return implode(' | ', $lines);
        }
        return null;
    }

    
    public function buildGw2Names()
    {
        $storage = $this->environment->getStorage();
        if ($storage instanceof MongoStorage) {

            // upgrades
            $upgrades   = [];
            $collection = $storage->getCollection('en', 'items');
            foreach ($collection->find(['data.type' => 'UpgradeComponent']) as $row) {
                if (isset($row['data']['name'])) {
                    $group                   = strtolower($row['data']['rarity']);
                    $name                    = strtolower($row['data']['name']);
                    $upgrades[$group][$name] = $row['data']['id'];
                }
            }
            ksort($upgrades);
            foreach ($upgrades as &$submap) {
                ksort($submap);
            }

            // specializations
            $specializations = [];
            $collection      = $storage->getCollection('en', 'specializations');
            foreach ($collection->find() as $row) {
                if (isset($row['data']['name'])) {
                    $group                          = strtolower($row['data']['profession']);
                    $name                           = strtolower($row['data']['name']);
                    $specializations[$group][$name] = $row['data']['id'];
                }
            }
            ksort($specializations);
            foreach ($specializations as &$submap) {
                ksort($submap);
            }

            // buffs
            $buffs      = [];
            $buffstats  = [];
            $collection = $storage->getCollection('en', 'items');
            foreach ($collection->find(['data.type' => 'Consumable', 'data.details.type' => ['$in' => ['Utility', 'Food']]]) as $row) {
                if (isset($row['data']['name'])) {
                    $name         = strtolower($row['data']['name']);
                    $buffs[$name] = $row['data']['id'];
                    $buffstat     = $this->getBuffStatKey($row['data']);
                    if ($buffstat) {
                        $buffstats['bystat'][$buffstat][] = $name;
                        $buffstats['byname'][$name]       = $buffstat;
                    }
                }
            }
            ksort($buffs);

            // pets
            $pets       = [];
            $collection = $storage->getCollection('en', 'pets');
            foreach ($collection->find() as $row) {
                if (isset($row['data']['name'])) {
                    $name              = strtolower($row['data']['name']);
                    $name              = str_replace('juvenile', '', $name);
                    $pets[trim($name)] = $row['data']['id'];
                }
            }
            ksort($pets);

            // pvp items
            $pvp_items  = [];
            $collection = $storage->getCollection('en', 'pvpamulets');
            foreach ($collection->find() as $row) {
                if (isset($row['data']['name'])) {
                    $name             = strtolower($row['data']['name']);
                    $name             = str_replace("'s", '', $name);
                    $pvp_items[$name] = $row['data']['id'];
                }
            }
            ksort($pvp_items);

            // traits
            $traits     = [];
            $collection = $storage->getCollection('en', 'traits');
            foreach ($collection->find() as $row) {
                if (isset($row['data']['name'])) {
                    $group                 = strtolower($row['data']['specialization']);
                    $name                  = strtolower($row['data']['name']);
                    $traits[$group][$name] = $row['data']['id'];
                }
            }
            ksort($traits);
            foreach ($traits as &$submap) {
                ksort($submap);
            }

            // skills
            $skills     = [];
            $collection = $storage->getCollection('en', 'skills');
            foreach ($collection->find() as $row) {
                if (isset($row['data']['name'], $row['data']['professions'])) {
                    $name        = strtolower($row['data']['name']);
                    $professions = $row['data']['professions'];
                    if (empty($professions) || !\is_array($professions)) {
                        $professions = [''];
                    }
                    foreach ($professions as $profession) {
                        $skills[strtolower($profession)][$name][] = $row['data']['id'];
                    }
                }
            }
            ksort($skills);
            foreach ($skills as &$submap) {
                ksort($submap);
            }

            $filename = $this->files['gw2names'];
            $this->exportToFile($filename, [
                'upgrades'        => $upgrades,
                'specializations' => $specializations,
                'traits'          => $traits,
                'buffs'           => $buffs,
                'buffstats'       => $buffstats,
                'skills'          => $skills,
                'pvp_items'       => $pvp_items,
                'pets'            => $pets,
            ]);
            return true;
        }
        return false;
    }

    /**
     *
     * @param $key
     * @return array
     * @throws RequestException
     */
    public function getMap($key = null)
    {
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
     * @return array
     * @throws RequestException
     */
    public function getData()
    {
        if (!isset($this->data)) {
            $filename = $this->files['alldata'];
            if (!is_file($filename)) {
                throw new RequestException('GW2 Skills data file cannot be found');
            }
            $this->data = include($filename);
        }
        return $this->data;
    }

    
    protected function reloadData()
    {
        try {
            $data = $this->request('all/');
        } catch (\Exception $ex) {
            // if the request fail, we skip it totally : silent
            // it happens if the requestor has not the right to access gw2 skills api
            return;
        }
        if (!\is_array($data)) {
            throw new RequestException('GW2 Skills data is not a valid array');
        }
        $filename = $this->files['alldata'];
        $this->exportToFile($filename, $data);

        $this->buildGw2Names();
        $this->buildMap();
    }

    /**
     *
     * @param $uri
     * @return array
     * @throws RequestException
     */
    protected function request($uri)
    {
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
