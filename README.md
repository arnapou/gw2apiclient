GW2 Api Client
==================

This project can be used to request GW2 api through PHP.

The classes I wrote are robust : retries when the api is not well responding, it manages long list of ids (making several requests and returning to you the whole response), and so on.


### Init objects

```php
// init Environment
$env = new Arnapou\GW2Api\Environment();

// init cache (here it is a Mongo cache, but it can be a file cache or whatever you want)
$mongo   = MongoDB\Client('mongodb://localhost:27017', [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
$mongoDB = $mongo->selectDatabase("my_db");
$cache   = new Arnapou\GW2Api\Cache\MongoCache($mongoDB);
$env->setCache($cache);

// init storage (optional, but recommended, here, we used the same mongo database as before)
$storage = new Arnapou\GW2Api\Storage\MongoStorage($mongoDB);
$env->setStorage($storage);

// set lang (en, fr, de, es)
$env->setLang('en');

// set access token
$env->setAccessToken('A7B98574-1757-8048-B640-55C2D3F46727BB6E108E-E011-4501-B0BB-B2731E90785D');
```

### Browse account through objects (use storage if set)

```php
$account = new Arnapou\GW2Api\Model\Account($env);

$account->getWorld()->getName();

$account->getCharacter('My Character Name')->getEquipment('Helm')->getName();
```

### Use api to retrieve raw api data (ignore storage, only cache is used)

```php
$env->getClientVersion1()->apiColors();

$env->getClientVersion2()->apiItems([21141,65230]);
```

### Fully custom use 

```php
// get api raw data
$data = $env->getClientVersion2()->apiPets([28]);

// instanciate object with data and use it as you wish
$pet = new Arnapou\GW2Api\Model\Pet($env, $data[0]);
$pet->getName();
```

Requirements
==================

* PHP 7+
* PECL extension mongodb version >=1.1.0 and <2.0.0
