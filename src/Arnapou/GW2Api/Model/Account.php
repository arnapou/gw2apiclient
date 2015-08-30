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
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Account extends AbstractObject {

    // PERMISSIONS
    const PERMISSION_ACCOUNT     = 'account';
    const PERMISSION_CHARACTERS  = 'characters';
    const PERMISSION_INVENTORIES = 'inventories';
    const PERMISSION_TRADINGPOST = 'tradingpost';
    const PERMISSION_UNLOCKS     = 'unlocks';
    const PERMISSION_WALLET      = 'wallet';

    /**
     *
     * @var array
     */
    protected $dataTokeninfo;

    /**
     *
     * @var array
     */
    protected $characters;

    /**
     *
     * @var array
     */
    protected $characterNames;

    /**
     *
     * @var array
     */
    protected $wallet;

    /**
     *
     * @var array
     */
    protected $bankVaults;

    /**
     *
     * @var array
     */
    protected $collectibles;

    /**
     *
     * @var array
     */
    protected $guilds;

    /**
     *
     * @var World
     */
    protected $world;

    /**
     *
     * @var string
     */
    protected $accessToken;

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
            self::PERMISSION_UNLOCKS,
            self::PERMISSION_WALLET,
        ];
    }

    /**
     * 
     * @param SimpleClient $client
     * @param string $accessToken
     */
    public function __construct(SimpleClient $client, $accessToken) {
        parent::__construct($client);

        $this->client->setAccessToken($accessToken);

        // get token info to check access token and permissions
        $infos = $this->client->getClientV2()->apiTokeninfo()->execute(7 * 86400)->getAllData();
        if (!isset($infos['id'])) {
            throw new InvalidTokenException('Invalid token.');
        }
        if (!isset($infos['permissions']) || !is_array($infos)) {
            throw new InvalidTokenException('Token info permission is missing, a weird bug occurs.');
        }
        sort($infos['permissions']);
        $this->dataTokeninfo = $infos;
        $this->accessToken   = $accessToken;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * 
     */
    protected function checkAccount() {
        if (empty($this->data)) {
            if (!$this->hasPermission(self::PERMISSION_ACCOUNT)) {
                throw new MissingPermissionException(self::PERMISSION_ACCOUNT);
            }
            $this->data = $this->client->v2_account();
        }
    }

    /**
     * 
     * @return array
     */
    public function getWallet() {
        if (empty($this->wallet)) {

            if (!$this->hasPermission(self::PERMISSION_WALLET)) {
                throw new MissingPermissionException(self::PERMISSION_WALLET);
            }

            $currencies = $this->client->v2_currencies($this->client->v2_currencies());
            usort($currencies, function($a, $b) {
                if ($a['order'] == $b['order']) {
                    return 0;
                }
                return $a['order'] > $b['order'] ? 1 : -1;
            });

            $quantities = [];
            foreach ($this->client->v2_account_wallet() as $item) {
                $quantities[$item['id']] = $item['value'];
            }

            $this->wallet = [];
            foreach ($currencies as $currency) {
                $currency['quantity'] = isset($quantities[$currency['id']]) ? $quantities[$currency['id']] : 0;
                $this->wallet[]       = new Currency($this->client, $currency);
            }
        }
        return $this->wallet;
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

            $slots = $this->client->v2_account_bank();
            $this->prepareSlots($slots);
            $this->prepareFlush();

            $vaults           = array_chunk($slots, 30);
            $this->bankVaults = [];
            foreach ($vaults as $i => $items) {
                $this->bankVaults[] = new BankVault($this->client, [
                    'id'    => $i + 1,
                    'items' => $items,
                ]);
            }
        }
        return $this->bankVaults;
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

            $categories = $this->client->v2_materials($this->client->v2_materials());
            foreach ($categories as $category) {
                $this->prepareItemIds($category['items']);
            }
            $this->prepareFlush();

            $materials = [];
            foreach ($categories as $category) {
                $items = [];
                foreach ($category['items'] as $id) {
                    $items[$id] = ['id' => $id];
                }
                $materials[$category['id']] = [
                    'id'    => $category['id'],
                    'name'  => $category['name'],
                    'items' => $items,
                ];
            }

            foreach ($this->client->v2_account_materials() as $item) {
                if (isset($item['category'], $item['count'], $item['id'])) {
                    if (isset($materials[$item['category']])) {
                        if (array_key_exists($item['id'], $materials[$item['category']]['items'])) {
                            $materials[$item['category']]['items'][$item['id']]['count'] = $item['count'];
                        }
                    }
                }
            }

            $this->collectibles = [];
            foreach ($materials as $category) {
                $this->collectibles[] = new CollectibleCategory($this->client, $category);
            }
        }
        return $this->collectibles;
    }

    /**
     * 
     * @return array
     */
    public function getCharacters() {
        if (empty($this->characters)) {
            if (!$this->hasPermission(self::PERMISSION_CHARACTERS)) {
                throw new MissingPermissionException(self::PERMISSION_CHARACTERS);
            }

            $characterNames = $this->client->v2_characters();

            // init for better performance
            $this->apiCharacters($characterNames);

            $characters = [];
            foreach ($characterNames as $characterName) {
                $characters[$characterName] = new Character($this->client, $characterName);
            }
            uasort($characters, function($a, $b) {
                if ($a->getAge() == $b->getAge()) {
                    return 0;
                }
                return $a->getAge() < $b->getAge() ? 1 : -1;
            });

            $this->characters = $characters;
        }
        return $this->characters;
    }

    /**
     * 
     * @return array
     */
    public function getCharacterNames() {
        if (empty($this->characterNames)) {
            if (!$this->hasPermission(self::PERMISSION_CHARACTERS)) {
                throw new MissingPermissionException(self::PERMISSION_CHARACTERS);
            }

            $this->characterNames = $this->client->v2_characters();
            sort($this->characterNames);
        }
        return $this->characterNames;
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
     * @return string
     */
    public function getId() {
        $this->checkAccount();
        return $this->data['id'];
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        $this->checkAccount();
        return $this->data['name'];
    }

    /**
     * 
     * @return World
     */
    public function getWorld() {
        $this->checkAccount();
        if (!isset($this->world)) {
            $this->world = new World($this->client, $this->data['world']);
        }
        return $this->world;
    }

    /**
     * 
     * @return array
     */
    public function getGuilds() {
        $this->checkAccount();
        if (!isset($this->guilds)) {
            $this->guilds = [];
            if (!empty($this->data['guilds']) && is_array($this->data['guilds'])) {
                foreach ($this->data['guilds'] as $guild) {
                    $this->guilds[] = new Guild($this->client, $guild);
                }
            }
        }
        return $this->guilds;
    }

    /**
     * 
     * @return arrya
     */
    public function getPermissions() {
        return $this->dataTokeninfo['permissions'];
    }

    /**
     * 
     * @param string $permission
     * @return boolean
     */
    public function hasPermission($permission) {
        return in_array($permission, $this->dataTokeninfo['permissions']);
    }

    /**
     * 
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getCreated() {
        if (isset($this->data['created'])) {
            return gmdate('Y-m-d H:i', strtotime($this->data['created']));
        }
        return null;
    }

}
