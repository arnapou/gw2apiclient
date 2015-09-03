GW2 Api Client
==================

This project can be used to request GW2 api through PHP.

### Requesting v2 worlds endpoint

    use Arnapou\GW2Api\Core\AbstractClient;
    use Arnapou\GW2Api\SimpleClient;
    
    $client = SimpleClient::create(AbstractClient::LANG_EN);
    
    // get all world ids
    $worldIds = $client->v2_worlds();
    
    // get specific worlds details
    $worlds = $client->v2_worlds([1001, 2003]);
    
    print_r($worlds);
