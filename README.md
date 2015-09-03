GW2 Api Client
==================

This project can be used to request GW2 api through PHP.

### Simple and fast use (no cache: bad idea)

    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\SimpleClient;
    
    // init client
    $client = SimpleClient::create(AbstractClient::LANG_EN);
    
    // get all world ids
    $worldIds = $client->v2_worlds();
    print_r($worldIds);
    
    // get specific worlds details
    $worlds = $client->v2_worlds([1001, 2003]);
    print_r($world);
    
    // get item detail
    $item = $client->v2_items([30689]);
    print_r($item);

### More customized with cache and retention

    use Arnapou\GW2Api\Cache\FileCache;
    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\SimpleClient;
    
    // use a file cache
    $cache = new FileCache($some_path_to_be_defined);
    
    // init client
    $client = SimpleClient::create(AbstractClient::LANG_EN, $cache);
    
    // get all world ids
    $worldIds = $client->getClientV2()
        ->apiWorlds()
        ->execute($cacheRetention)
        ->getAllData();
    print_r($worldIds);
    
    // get specific worlds details
    $worlds = $client->getClientV2()
        ->apiWorlds([1001, 2003])
        ->execute($cacheRetention)
        ->getAllData();
    print_r($world);
    
    // get item detail
    $item = $client->getClientV2()
        ->apiItems([30689])
        ->execute($cacheRetention)
        ->getAllData();
    print_r($item);

### Use of access token for authentified Api requests

    use Arnapou\GW2Api\Cache\MongoCache;
    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\SimpleClient;
    
    // use a mongo cache
    $cache = new MongoCache($mongoCollection);
    
    // init client
    $client = SimpleClient::create(AbstractClient::LANG_DE, $cache);
    
    // set access token
    $client->setAccessToken($accessToken);
    
    // get the account wallet details
    $wallet = $client->v2_account_wallet();
    print_r($wallet);

### Even deeper in the client api to control the requester

    use Arnapou\GW2Api\Cache\MemcachedCache;
    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\Core\RequestManager;
    use Arnapou\GW2Api\Core\ClientV1;
    
    // use a memcached cache
    $cache = new MemcachedCache();
    
    // init request manager
    $requestManager = new RequestManager();
    $requestManager->setCache($cache);
    $requestManager->setCurlRequestTimeout(10);
    $requestManager->setCurlUserAgent('My user Agent');
    $requestManager->setDefautCacheRetention(900); // 15 min
    
    // init client v1
    $client = new ClientV1($requestManager);
    $client->setLang(AbstractClient::LANG_ES);
    
    // get one guild details
    $guild = $client->apiGuildDetails($guildId)
        ->execute()
        ->getAllData();
    print_r($guild);

### And the magic come here with simple model classes

    use Arnapou\GW2Api\Cache\MongoCache;
    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\SimpleClient;
    use Arnapou\GW2Api\Model\Account;
    
    // use a mongo cache
    $cache = new MongoCache($mongoCollection);
    
    // init client
    $client = SimpleClient::create(AbstractClient::LANG_DE, $cache);
    
    // init account object
    $account = new Account($client, $accessToken);
    
    // get character names
    $account->getCharacterNames();
    
    // get one character
    $char = $account->getCharacter('My Character');
    
    // get info on the character
    $char->getAge();
    $char->getCrafting();
    $char->getGuild()->getFullname();
    ...
    
    // get char helm
    $helm = $char->getEquipment('Helm');
    
    // get info on the helm
    $helm->getName();
    $helm->getAttributes();
    $helm->getRarity();
    $helm->getAgonyResistance();
    ...
