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

class MongoCache implements CacheInterface, MultipleGetCacheInterface {

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
    protected $collectionPrefix;

    /**
     *
     * @var \MongoCollection
     */
    protected $collection;

    /**
     *
     * @var \MongoDB
     */
    protected $mongoDB;

    /**
     *
     * @var array
     */
    protected $objectCollections = [];

    /**
     * 
     * @param \MongoDB $mongoDB
     * @param string $collectionPrefix
     */
    public function __construct(\MongoDB $mongoDB, $collectionPrefix = 'cache') {
        $this->collectionPrefix = $collectionPrefix;
        $this->mongoDB          = $mongoDB;
        $this->collection       = $mongoDB->selectCollection($collectionPrefix . '_requests');
        $this->collection->ensureIndex(['key' => 1], ['unique' => true]);
        $this->collection->ensureIndex(['expiration' => 1]);
    }

    /**
     * 
     * @param string $collectionSuffixName
     * @return \MongoCollection
     */
    public function getMongoCollection($collectionSuffixName) {
        if (!isset($this->objectCollections[$collectionSuffixName])) {
            $collection                                     = $this->mongoDB->selectCollection($this->collectionPrefix . '_' . $collectionSuffixName);
            $collection->ensureIndex(['key' => 1], ['unique' => true]);
            $collection->ensureIndex(['expiration' => 1]);
            $collection->ensureIndex(['value.id' => 1]);
            $collection->ensureIndex(['value.type' => 1]);
            $collection->ensureIndex(['value.skin' => 1]);
            $collection->ensureIndex(['value.details.type' => 1]);
            $this->objectCollections[$collectionSuffixName] = $collection;
        }
        return $this->objectCollections[$collectionSuffixName];
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    protected function detectCollectionName($key) {
        if (strpos($key, 'smartCaching/') === 0) {
            $elements = explode('/', $key);
            return $elements[1];
        }
        return 'requests';
    }

    /**
     * 
     * @param string $key
     * @return \MongoCollection
     */
    protected function detectCollection($key) {
        if (strpos($key, 'smartCaching/') === 0) {
            $elements = explode('/', $key);
            return $this->getMongoCollection($elements[1]);
        }
        else {
            return $this->collection;
        }
    }

    /**
     * 
     * @return \MongoDB
     */
    public function getMongoDB() {
        return $this->mongoDB;
    }

    /**
     * Ran when php exits : automatically run of GC if conditions are met
     */
    public function __destruct() {
        $rand = mt_rand(1, $this->gcDivisor);
        if ($rand <= $this->gcProbability) {
            $this->runGarbageCollector();
        }
    }

    /**
     * Run the garbage collector which clean expired files
     */
    public function runGarbageCollector() {
        try {
            $this->collection->remove(['expiration' => ['$lt' => time()]]);
        }
        catch (\Exception $e) {
            return null;
        }
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
     */
    public function setGarbageCollectorParameters($gcProbability, $gcDivisor) {
        if ($gcDivisor < 1) {
            throw new Exception('gcDivisor should be strictly > 0.');
        }
        if ($gcDivisor < 0) {
            throw new Exception('gcProbability cannot be negative.');
        }
        $this->gcDivisor     = $gcDivisor;
        $this->gcProbability = $gcProbability;
    }

    protected function hash($key) {
        return hash('sha256', $key);
    }

    public function get($key) {
        $document = $this->detectCollection($key)->findOne([
            'key'        => $this->hash($key),
            'expiration' => ['$gte' => time()],
        ]);
        if ($document && isset($document['value'])) {
            try {
                return $document['value'];
            }
            catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 
     * @param array $keys
     * @return array
     */
    public function getMultiple($keys) {
        $return           = [];
        $keysByCollection = [];
        foreach ($keys as $key) {
            $keysByCollection[$this->detectCollectionName($key)][] = $this->hash($key);
        }
        foreach ($keysByCollection as $collectioName => $hashs) {
            $documents = $this->getMongoCollection($collectioName)
                ->find([
                'key'        => ['$in' => $hashs],
                'expiration' => ['$gte' => time()],
            ]);
            foreach ($documents as $document) {
                if ($document && isset($document['value'])) {
                    try {
                        $return[] = $document['value'];
                    }
                    catch (\Exception $e) {
                        
                    }
                }
            }
        }
        return $return;
    }

    public function exists($key) {
        $document = $this->detectCollection($key)->findOne([
            'key'        => $this->hash($key),
            'expiration' => ['$gte' => time()],
        ]);
        if ($document && isset($document['value'])) {
            return true;
        }
        return null;
    }

    public function set($key, $value, $expiration = 0) {
        if ($expiration != 0 && $expiration <= 30 * 86400) {
            $expiration += time();
        }
        $hash = $this->hash($key);
        $this->detectCollection($key)->update([
            'key' => $hash,
            ], [
            'key'        => $hash,
            'value'      => $value,
            'expiration' => $expiration,
            ], [
            'upsert' => true
        ]);
    }

    public function remove($key) {
        try {
            $this->detectCollection($key)->remove(['key' => $this->hash($key)]);
        }
        catch (\Exception $e) {
            return null;
        }
    }

}
