<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Cache;

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Exception\WrongMongoDatabaseException;
use MongoDB\Collection as MongoCollection;
use MongoDB\Database as MongoDatabase;

class MongoCache implements CacheInterface
{
    /**
     *
     * @var int
     */
    protected $gcProbability = 100;

    /**
     *
     * @var int
     */
    protected $gcDivisor = 100000;

    /**
     *
     * @var string
     */
    protected $collectionName;

    /**
     *
     * @var MongoCollection
     */
    protected $collection;

    /**
     *
     * @var MongoDatabase
     */
    protected $mongoDB;

    /**
     *
     * Example to instanciate a valid mongoDB variable : <pre>
     *     $mongo   = new MongoDB\Client('mongodb://localhost:27017', [], ['typeMap' => ['root' => 'array', 'document'
     *     => 'array']]);
     *     $mongoDB = $mongo->selectDatabase("test");
     * </pre>
     *
     * @param MongoDatabase $mongoDB
     * @param string        $collectionName
     * @throws WrongMongoDatabaseException
     */
    public function __construct(MongoDatabase $mongoDB, $collectionName = 'cache')
    {
        $error = \Arnapou\GW2Api\get_mongo_database_error($mongoDB);
        if ($error) {
            throw new WrongMongoDatabaseException($error);
        }
        $this->collectionName = $collectionName;
        $this->mongoDB        = $mongoDB;
        $this->collection     = $mongoDB->selectCollection($collectionName);
        $this->collection->createIndex(['key' => 1], ['unique' => true]);
        $this->collection->createIndex(['expiration' => 1]);
    }

    /**
     * Ran when php exits : automatically run of GC if conditions are met
     */
    public function __destruct()
    {
        $rand = mt_rand(1, $this->gcDivisor);
        if ($rand <= $this->gcProbability) {
            $this->runGarbageCollector();
        }
    }

    /**
     * Run the garbage collector which clean expired files
     */
    public function runGarbageCollector()
    {
        try {
            $this->collection->deleteMany(['expiration' => ['$lt' => time()]]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     *
     * @return MongoDatabase
     */
    public function getMongoDB()
    {
        return $this->mongoDB;
    }

    /**
     *
     * @return MongoCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * Set the probability for the garbage collector to clean expired
     * data (gcProbability/gcDivisor) when the script finishes.
     *
     * If gcProbability = 0 then the garbage collector will never run.
     *
     * If gcProbability > gcDivisor then the garbage collector will always run.
     *
     * @param int $gcProbability
     * @param int $gcDivisor
     * @throws Exception
     */
    public function setGarbageCollectorParameters($gcProbability, $gcDivisor)
    {
        if ($gcDivisor < 1) {
            throw new Exception('gcDivisor should be strictly > 0.');
        }
        if ($gcDivisor < 0) {
            throw new Exception('gcProbability cannot be negative.');
        }
        $this->gcDivisor     = $gcDivisor;
        $this->gcProbability = $gcProbability;
    }

    protected function hash($key)
    {
        return hash('sha256', $key);
    }

    public function get($key)
    {
        $document = $this->collection->findOne([
            'key'        => $this->hash($key),
            'expiration' => ['$gte' => time()],
        ]);
        if ($document && isset($document['value'])) {
            try {
                return $document['value'];
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function exists($key)
    {
        $document = $this->collection->findOne([
            'key'        => $this->hash($key),
            'expiration' => ['$gte' => time()],
        ]);
        if ($document && isset($document['value'])) {
            return true;
        }
        return null;
    }

    public function set($key, $value, $expiration = 0)
    {
        if ($expiration != 0 && $expiration <= 30 * 86400) {
            $expiration += time();
        }
        $hash = $this->hash($key);
        $this->collection->updateOne([
            'key' => $hash,
        ], [
            '$set' => [
                'key'        => $hash,
                'value'      => $value,
                'expiration' => $expiration,
            ],
        ], [
            'upsert' => true,
        ]);
    }

    public function remove($key)
    {
        try {
            $this->collection->deleteOne(['key' => $this->hash($key)]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
