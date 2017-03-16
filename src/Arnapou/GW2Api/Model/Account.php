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
use Arnapou\GW2Api\Exception\InvalidTokenException;
use Arnapou\GW2Api\Exception\MissingPermissionException;
use Arnapou\GW2Api\Environment;

/**
 * 
 */
class Account extends AbstractObject {
    /*
     * account     : Your account display name, ID, home world, and list of guilds. Required permission.
     * inventories : Your account bank, material storage, recipe unlocks, and character inventories.
     * characters  : Basic information about your characters.
     * tradingpost : Your Trading Post transactions.
     * wallet      : Your account's wallet.
     * unlocks     : Your wardrobe unlocks—skins, dyes, minipets, finishers, etc.—and currently equipped skins.
     * pvp         : Your PvP stats, match history, reward track progression, and custom arena details.
     * builds      : Your currently equipped specializations, traits, skills, and equipment for all game modes.
     * progression : Your achievements, dungeon unlock status, mastery point assignments, and general PvE progress.
     * guilds      : La liste des membres, l'historique et le message du jour de toutes les guildes dont vous êtes membre.
     */

    const PERMISSION_ACCOUNT     = 'account';
    const PERMISSION_INVENTORIES = 'inventories';
    const PERMISSION_CHARACTERS  = 'characters';
    const PERMISSION_TRADINGPOST = 'tradingpost';
    const PERMISSION_WALLET      = 'wallet';
    const PERMISSION_UNLOCKS     = 'unlocks';
    const PERMISSION_PVP         = 'pvp';
    const PERMISSION_BUILDS      = 'builds';
    const PERMISSION_PROGRESSION = 'progression';
    const PERMISSION_GUILDS      = 'guilds';

    /**
     * 
     * @return array
     */
    public static function permissionsList() {
        return [
            self::PERMISSION_ACCOUNT,
            self::PERMISSION_CHARACTERS,
            self::PERMISSION_INVENTORIES,
            self::PERMISSION_TRADINGPOST,
            self::PERMISSION_WALLET,
            self::PERMISSION_UNLOCKS,
            self::PERMISSION_PVP,
            self::PERMISSION_BUILDS,
            self::PERMISSION_PROGRESSION,
            self::PERMISSION_GUILDS,
        ];
    }

    /*
     * game access constants
     */

    const GAME_ACCESS_CORE = 'GuildWars2';
    const GAME_ACCESS_HOT  = 'HeartOfThorns';

    /**
     *
     * @var array
     */
    private $dataTokenInfo;

    /**
     *
     * @var array
     */
    private $dataAccount;

    /**
     *
     * @var array
     */
    private $dataCharacters;

    /**
     *
     * @var array
     */
    private $dataCharacterNames;

    /**
     *
     * @var World
     */
    private $world;

    /**
     *
     * @var array
     */
    private $guilds;

    /**
     *
     * @var array
     */
    private $wallet;

    /**
     *
     * @var array
     */
    private $inventory;

    /**
     *
     * @var WvwMatch
     */
    private $wvwMatch;

    /**
     *
     * @var array
     */
    private $bankVaults;

    /**
     *
     * @var array
     */
    private $accountAchievements = [];

    /**
     *
     * @var array
     */
    private $achievementsGroups = [];

    /**
     *
     * @var array
     */
    private $storySeasons = [];

    /**
     *
     * @var array
     */
    private $achievementsDaily = [];

    /**
     *
     * @var array
     */
    protected $collectibles;

    /**
     *
     * @var array
     */
    protected $dungeons = [];

    /**
     *
     * @var array
     */
    protected $raids = [];

    /**
     *
     * @var array
     */
    protected $masteries = [];

    /**
     *
     * @var integer
     */
    protected $otherAP;

    /**
     *
     * @var Pvp
     */
    protected $pvp;

    /**
     *
     * @var Wardrobe
     */
    protected $wardrobe;

    /**
     *
     * @var array
     */
    protected $dyes;

    /**
     *
     * @var array
     */
    protected $minis;

    /**
     *
     * @var array
     */
    protected $titles;

    /**
     *
     * @var array
     */
    protected $finishers;

    /**
     *
     * @var array
     */
    protected $gliders;

    /**
     *
     * @var array
     */
    protected $outfits;

    /**
     *
     * @var array
     */
    protected $tradingPost;

    /**
     * 
     */
    public function __construct(Environment $environment) {
        parent::__construct($environment, null);

        if (empty($this->getEnvironment()->getAccessToken())) {
            throw new Exception('You should provide the access token before using Account class.');
        }

        // token info
        $tokenInfo = $this->getEnvironment()->getClientVersion2()->apiTokeninfo();
        if (!isset($tokenInfo['id'])) {
            throw new InvalidTokenException('Invalid token.');
        }
        if (!isset($tokenInfo['permissions']) || !is_array($tokenInfo)) {
            throw new InvalidTokenException('Token info permission is missing, a weird bug occurs.');
        }
        sort($tokenInfo['permissions']);
        $this->dataTokenInfo = $tokenInfo;

        // account
        $account = $this->getEnvironment()->getClientVersion2()->apiAccount();
        if (isset($account['text'])) {
            if (stripos($account['text'], 'invalid key') !== null) {
                throw new InvalidTokenException('Invalid token.');
            }
            throw new Exception($account['text']);
        }
        if (!$this->hasPermission(self::PERMISSION_ACCOUNT)) {
            throw new MissingPermissionException(self::PERMISSION_ACCOUNT);
        }
        $this->dataAccount = $account;
    }

    /**
     * 
     * @return array
     */
    public function getAccountAchievements() {
        if (empty($this->accountAchievements)) {

            if (!$this->hasPermission(self::PERMISSION_PROGRESSION)) {
                throw new MissingPermissionException(self::PERMISSION_PROGRESSION);
            }

            $env          = $this->getEnvironment();
            $achievements = [];
            foreach ($env->getClientVersion2()->apiAccountAchievements() as $data) {
                if (isset($data['id'])) {
                    $achievements[$data['id']] = new AccountAchievement($env, $data);
                }
            }

            $this->accountAchievements = $achievements;
        }
        return $this->accountAchievements;
    }

    /**
     * 
     * @return TradingPost
     */
    public function getTradingPost() {
        if (empty($this->tradingPost)) {

            if (!$this->hasPermission(self::PERMISSION_TRADINGPOST)) {
                throw new MissingPermissionException(self::PERMISSION_TRADINGPOST);
            }

            $this->tradingPost = new TradingPost($this->getEnvironment(), []);
        }
        return $this->tradingPost;
    }

    /**
     * 
     * @return Gliders
     */
    public function getGliders() {
        if (empty($this->gliders)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env           = $this->getEnvironment();
            $this->gliders = new Gliders($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountGliders(),
            ]);
        }
        return $this->gliders;
    }

    /**
     * 
     * @return Finishers
     */
    public function getFinishers() {
        if (empty($this->finishers)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env             = $this->getEnvironment();
            $this->finishers = new Finishers($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountFinishers(),
            ]);
        }
        return $this->finishers;
    }

    /**
     * 
     * @return Outfits
     */
    public function getOutfits() {
        if (empty($this->outfits)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env           = $this->getEnvironment();
            $this->outfits = new Outfits($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountOutfits(),
            ]);
        }
        return $this->outfits;
    }

    /**
     * 
     * @return Titles
     */
    public function getTitles() {
        if (empty($this->titles)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env          = $this->getEnvironment();
            $this->titles = new Titles($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountTitles(),
            ]);
        }
        return $this->titles;
    }

    /**
     * 
     * @return Minis
     */
    public function getMinis() {
        if (empty($this->minis)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env         = $this->getEnvironment();
            $this->minis = new Minis($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountMinis(),
            ]);
        }
        return $this->minis;
    }

    /**
     * 
     * @return Dyes
     */
    public function getDyes() {
        if (empty($this->dyes)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env        = $this->getEnvironment();
            $this->dyes = new Dyes($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountDyes(),
            ]);
        }
        return $this->dyes;
    }

    /**
     * 
     * @return Wardrobe
     */
    public function getWardrobe() {
        if (empty($this->wardrobe)) {

            if (!$this->hasPermission(self::PERMISSION_UNLOCKS)) {
                throw new MissingPermissionException(self::PERMISSION_UNLOCKS);
            }

            $env            = $this->getEnvironment();
            $this->wardrobe = new Wardrobe($env, [
                'unlocked' => $env->getClientVersion2()->apiAccountSkins(),
            ]);
        }
        return $this->wardrobe;
    }

    /**
     * 
     * @return Pvp
     */
    public function getPvp() {
        if (empty($this->pvp)) {

            if (!$this->hasPermission(self::PERMISSION_PVP)) {
                throw new MissingPermissionException(self::PERMISSION_PVP);
            }

            $data      = $this->getEnvironment()->getClientVersion2()->apiPvpStats();
            $this->pvp = new Pvp($this->getEnvironment(), $data);
        }
        return $this->pvp;
    }

    /**
     * 
     * @return array
     */
    public function getCollectibles() {
        if (empty($this->collectibles)) {

            if (!$this->hasPermission(self::PERMISSION_INVENTORIES)) {
                throw new MissingPermissionException(self::PERMISSION_INVENTORIES);
            }

            $env = $this->getEnvironment();

            $preload = [];
            $items   = [];
            foreach ($env->getClientVersion2()->apiAccountMaterials() as $data) {
                if (isset($data['id'], $data['count'], $data['category'])) {
                    $items[$data['category']][] = [
                        'id'    => $data['id'],
                        'count' => $data['count'],
                    ];

                    $preload[] = new Material($env, $data['category']);
                }
            }

            foreach ($env->getClientVersion2()->apiMaterials() as $materialId) {
                $this->collectibles[] = new CollectibleCategory($env, [
                    'id'    => $materialId,
                    'items' => isset($items[$materialId]) ? $items[$materialId] : [],
                ]);
            }

            usort($this->collectibles, function($a, $b) {
                $na = $a->getOrder();
                $nb = $b->getOrder();
                if ($na == $nb) {
                    return 0;
                }
                return $na > $nb ? 1 : -1;
            });
        }
        return $this->collectibles;
    }

    /**
     * 
     * @return array
     */
    public function getBankVaults() {
        if (empty($this->bankVaults)) {

            if (!$this->hasPermission(self::PERMISSION_INVENTORIES)) {
                throw new MissingPermissionException(self::PERMISSION_INVENTORIES);
            }

            $env   = $this->getEnvironment();
            $slots = $env->getClientVersion2()->apiAccountBank();

            $vaults           = array_chunk($slots, 30);
            $this->bankVaults = [];
            foreach ($vaults as $i => $items) {
                $this->bankVaults[] = new BankVault($env, [
                    'id'    => $i + 1,
                    'items' => $items,
                ]);
            }
        }
        return $this->bankVaults;
    }

    /**
     * 
     * @return array ["Name 1", "Name 2"]
     */
    public function getCharacterNames() {
        if (empty($this->dataCharacterNames)) {
            if (!$this->hasPermission(self::PERMISSION_CHARACTERS)) {
                throw new MissingPermissionException(self::PERMISSION_CHARACTERS);
            }

            $names = $this->getEnvironment()->getClientVersion2()->apiCharacters();

            $this->dataCharacterNames = $names;
        }
        return $this->dataCharacterNames;
    }

    /**
     * 
     * @return array
     */
    public function getCharacters() {
        if (empty($this->dataCharacters)) {
            if (!$this->hasPermission(self::PERMISSION_CHARACTERS)) {
                throw new MissingPermissionException(self::PERMISSION_CHARACTERS);
            }

            $items = $this->getEnvironment()->getClientVersion2()->apiCharacters($this->getCharacterNames());

            $characters = [];
            foreach ($items as $item) {
                if (isset($item['name'])) {
                    $characters[$item['name']] = new Character($this->getEnvironment(), $item);
                }
            }

            $this->dataCharacters = $characters;
        }
        return $this->dataCharacters;
    }

    /**
     * 
     * @param string $name
     * @return Character
     */
    public function getCharacter($name) {
        $characters = $this->getCharacters();
        if (isset($characters[$name])) {
            return $characters[$name];
        }
        return null;
    }

    /**
     * 
     * @return string "1E25809D-6A79-EE39-E111-736E8E79F0D1"
     */
    public function getId() {
        return $this->getData('id', null, $this->dataAccount);
    }

    /**
     * 
     * @return string "My Name.1234"
     */
    public function getName() {
        return $this->getData('name', null, $this->dataAccount);
    }

    /**
     * 
     * @return integer 2102
     */
    public function getWorldId() {
        return (int) $this->getData('world', null, $this->dataAccount);
    }

    /**
     * 
     * @return World
     */
    public function getWorld() {
        if (empty($this->world)) {
            $this->world = new World($this->getEnvironment(), $this->getWorldId());
        }
        return $this->world;
    }

    /**
     * 
     * @return array ["27E8635F-2B2F-44BC-A58F-03F66F2083E2", "52BD8E08-7F38-449E-ADB7-37CC6CE47230"]
     */
    public function getGuildIds($onlyLeader = false) {
        if ($onlyLeader) {
            return $this->getData('guild_leader', [], $this->dataAccount);
        }
        else {
            return $this->getData('guilds', [], $this->dataAccount);
        }
    }

    /**
     * 
     * @return World
     */
    public function getGuilds($onlyLeader = false) {
        if (empty($this->guilds)) {
            $this->guilds = [];
            $env          = $this->getEnvironment();
            $leaderIds    = $this->getGuildIds(true);
            foreach ($this->getGuildIds() as $id) {
                try {
                    $data = $env->getClientVersion2()->apiGuild($id);
                    if (isset($data['id'])) {
                        if (in_array($id, $leaderIds)) {
                            $data['x-leader'] = true;
                        }
                        $this->guilds[] = new Guild($env, $data);
                    }
                }
                catch (\Exception $e) {
                    
                }
            }
        }
        if ($onlyLeader) {
            $tmp = [];
            foreach ($this->guilds as /* @var $guild Guild */ $guild) {
                if ($guild->isLeader()) {
                    $tmp[] = $guild;
                }
            }
            return $tmp;
        }
        else {
            return $this->guilds;
        }
    }

    /**
     * 
     * @return World
     */
    public function getInventory() {
        if (empty($this->inventory)) {
            $this->inventory = [];
            $env             = $this->getEnvironment();
            $client          = $env->getClientVersion2();
            $inventory       = $client->apiAccountInventory();
            foreach ($inventory as $data) {
                $this->inventory[] = new InventorySlot($env, $data);
            }
        }
        return $this->inventory;
    }

    /**
     * 
     * @return World
     */
    public function getWallet() {
        if ($this->wallet === null) {
            $this->wallet = [];

            $env   = $this->getEnvironment();
            $items = $env->getClientVersion2()->apiAccountWallet();
            foreach ($items as $item) {
                if (isset($item['id'], $item['value'])) {
                    $currency                  = new Currency($env, $item['id']);
                    $currency->setQuantity($item['value']);
                    $this->wallet[$item['id']] = $currency;
                }
            }
            uasort($this->wallet, function($a, $b) {
                $orderA = $a->getOrder();
                $orderB = $b->getOrder();
                if ($orderA == $orderB) {
                    return 0;
                }
                return $orderA > $orderB ? 1 : -1;
            });
        }
        return $this->wallet;
    }

    /**
     * 
     * @return integer 100
     */
    public function getFractalLevel() {
        return (int) $this->getData('fractal_level', null, $this->dataAccount);
    }

    /**
     * 
     * @return integer 12309
     */
    public function getDailyAP() {
        return (int) $this->getData('daily_ap', null, $this->dataAccount);
    }

    /**
     * 
     * @return integer 2691
     */
    public function getMonthlyAP() {
        return (int) $this->getData('monthly_ap', null, $this->dataAccount);
    }

    /**
     * 
     * @return integer 
     */
    public function getOtherAP() {
        if ($this->otherAP === null && $this->hasPermission(self::PERMISSION_PROGRESSION)) {
            $this->otherAP = 0;
            $ids           = [];
            $aps           = $this->getAccountAchievements();
            foreach ($this->getAchievementsGroups() as /* @var $group AchievementGroup */ $group) {
                foreach ($group->getCategories() as /* @var $category AchievementCategory */ $category) {
                    foreach ($category->getAchievements() as /* @var $item Achievement */ $item) {
                        if (isset($aps[$item->getId()])) {
                            $this->otherAP += $aps[$item->getId()]->getTotalAP();
                            $ids[]         = $item->getId();
                        }
                    }
                }
            }
        }
        return $this->otherAP;
    }

    /**
     * 
     * @return integer 
     */
    public function getTotalAP() {
        return $this->getMonthlyAP() + $this->getDailyAP() + $this->getOtherAP();
    }

    /**
     * 
     * @return integer 245
     */
    public function getWvwRank() {
        return (int) $this->getData('wvw_rank', null, $this->dataAccount);
    }

    /**
     * 
     * @return boolean
     */
    public function hasAccessHeartOfThorns() {
        return $this->getData('access', null, $this->dataAccount) === self::GAME_ACCESS_HOT ? true : false;
    }

    /**
     * 
     * @return boolean
     */
    public function hasCommanderTag() {
        return $this->getData('commander', null, $this->dataAccount) == 1 ? true : false;
    }

    /**
     * 
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getCreated() {
        $date = $this->getData('created', null, $this->dataAccount);
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        return $this->getEnvironment()->getAccessToken();
    }

    /**
     * 
     * @param string $permission
     * @return boolean
     */
    public function hasPermission($permission) {
        return in_array($permission, $this->dataTokenInfo['permissions']);
    }

    /**
     * 
     * @return array
     */
    public function getDungeons() {
        if (empty($this->dungeons)) {
            $env    = $this->getEnvironment();
            $client = $env->getClientVersion2();
            foreach ($client->apiDungeons() as $id) {
                $this->dungeons[] = new Dungeon($env, $id);
            }
            $pathIds = $client->apiAccountDungeons();
            foreach ($this->dungeons as /* @var $dungeon Dungeon */ $dungeon) {
                foreach ($dungeon->getPaths() as /* @var $path DungeonPath */ $path) {
                    if (in_array($path->getId(), $pathIds)) {
                        $path->setUnlocked(true);
                    }
                }
            }
        }
        return $this->dungeons;
    }

    /**
     * 
     * @return array
     */
    public function getRaids() {
        if (empty($this->raids)) {
            $env    = $this->getEnvironment();
            $client = $env->getClientVersion2();
            foreach ($client->apiRaids() as $id) {
                $this->raids[] = new Raid($env, $id);
            }
            $eventIds = $client->apiAccountRaids();
            foreach ($this->raids as /* @var $raid Raid */ $raid) {
                foreach ($raid->getWings() as /* @var $wing RaidWing */ $wing) {
                    foreach ($wing->getEvents() as /* @var $event RaidWingEvent */ $event) {
                        if (in_array($event->getId(), $eventIds)) {
                            $event->setUnlocked(true);
                        }
                    }
                }
            }
        }
        return $this->raids;
    }

    /**
     * 
     * @return array
     */
    public function getAchievementsDaily() {
        if (empty($this->achievementsDaily)) {
            $env = $this->getEnvironment();
            foreach ($env->getClientVersion2()->apiAchievementsDaily() as $mode => $items) {
                foreach ($items as $item) {
                    $this->achievementsDaily[$mode][] = new AchievementDaily($env, $item);
                }
            }
        }
        return $this->achievementsDaily;
    }

    /**
     * 
     * @return array
     */
    public function getAchievementsGroups() {
        if (empty($this->achievementsGroups)) {
            $env = $this->getEnvironment();
            foreach ($env->getClientVersion2()->apiAchievementsGroups() as $id) {
                $this->achievementsGroups[$id] = new AchievementGroup($env, $id);
            }
            uasort($this->achievementsGroups, function($a, $b) {
                $ia = $a->getOrder();
                $ib = $b->getOrder();
                if ($ia == $ib) {
                    return 0;
                }
                return $ia < $ib ? -1 : 1;
            });
        }
        return $this->achievementsGroups;
    }

    /**
     * 
     * @return integer
     */
    public function getMasteriesSpentPoints() {
        $sum = 0;
        foreach ($this->getMasteries() as /* @var $mastery Mastery */ $mastery) {
            $sum += $mastery->getSpentPoints();
        }
        return $sum;
    }

    /**
     * 
     * @return array
     */
    public function getMasteries() {
        if (empty($this->masteries)) {
            $env      = $this->getEnvironment();
            $unlocked = [];
            if ($this->hasPermission(self::PERMISSION_PROGRESSION)) {
                $unlocked = [];
                foreach ($env->getClientVersion2()->apiAccountMasteries() as $data) {
                    if (isset($data['id'], $data['level'])) {
                        $unlocked[$data['id']] = $data['level'];
                    }
                }
            }

            foreach ($env->getClientVersion2()->apiMasteries() as $id) {
                $mastery = new Mastery($env, $id);
                if (isset($unlocked[$id])) {
                    $mastery->setUnlockedLevel($unlocked[$id]);
                }
                $this->masteries[$id] = $mastery;
            }
            uasort($this->masteries, function($a, $b) {
                $n = strcmp($a->getRegion(), $b->getRegion());
                if ($n != 0) {
                    return $n;
                }
                $ia = $a->getOrder();
                $ib = $b->getOrder();
                if ($ia == $ib) {
                    return 0;
                }
                return $ia < $ib ? -1 : 1;
            });
        }
        return $this->masteries;
    }

    /**
     * 
     * @return array
     */
    public function getStorySeasons() {
        if (empty($this->storySeasons)) {
            $env = $this->getEnvironment();
            foreach ($env->getClientVersion2()->apiStoriesSeasons() as $id) {
                $this->storySeasons[] = new StorySeason($env, $id);
            }
            usort($this->storySeasons, function(StorySeason $a, StorySeason $b) {
                $ia = $a->getOrder();
                $ib = $b->getOrder();
                if ($ia == $ib) {
                    return 0;
                }
                return $ia < $ib ? -1 : 1;
            });
        }
        return $this->storySeasons;
    }

    public function getWvwMatch() {
        if ($this->wvwMatch === null) {
            $env     = $this->getEnvironment();
            $ids     = $env->getClientVersion2()->apiWvwMatches();
            $matches = [];
            foreach ($ids as $id) {
                $matches[] = new WvwMatch($env, $id);
            }
            $worldId = $this->getWorldId();
            foreach ($matches as $match) {
                foreach ($match->getAllWorlds() as $color => $worlds) {
                    foreach ($worlds as $world) {
                        if ($world->getId() == $worldId) {
                            $this->wvwMatch = $match;
                            return $match;
                        }
                    }
                }
            }
        }
        return $this->wvwMatch;
    }

}
