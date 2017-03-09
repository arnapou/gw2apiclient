<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Storage;

use Arnapou\GW2Api\Exception\Exception;
use MongoDB\BSON\UTCDateTime as MongoDate;
use MongoDB\Database as MongoDatabase;
use MongoDB\Collection as MongoCollection;

class MongoStorage extends AbstractStorage {

    /**
     *
     * @var MongoDatabase
     */
    protected $mongoDB;

    /**
     *
     * @var string
     */
    protected $collectionPrefix;

    /**
     *
     * @var array
     */
    protected $collections = [];

    /**
     *
     * @var array
     */
    protected $indexes = [
        'items' => [
            'data.default_skin',
            'data.details.color_id',
        ],
    ];

    /**
     * 
     * Example to instanciate a valid mongoDB variable : <pre>
     *     $mongo   = new MongoDB\Client('mongodb://localhost:27017', [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
     *     $mongoDB = $mongo->selectDatabase("test");
     * </pre>
     * 
     * @param MongoDatabase $mongoDB
     */
    public function __construct(MongoDatabase $mongoDB, $collectionPrefix = 'storage_') {
        $error = \Arnapou\GW2Api\get_mongo_database_error($mongoDB);
        if ($error) {
            throw new WrongMongoDatabaseException($error);
        }
        $this->mongoDB          = $mongoDB;
        $this->collectionPrefix = $collectionPrefix;
    }

    public function set($lang, $name, $id, $data) {
        $this->getCollection($lang, $name)
            ->updateOne([
                'key' => (string) $id,
                ], [
                '$set' => [
                    'key'         => (string) $id,
                    'data'        => $data,
                    'datecreated' => new MongoDate(floor(microtime(true) * 1000)),
                ]
                ], [
                'upsert' => true
        ]);
        parent::set($lang, $name, $id, $data);
    }

    protected function loadFromFallback($lang, $name, $fallback, $ids) {

        $key        = $this->getKey($lang, $name);
        $collection = $this->getCollection($lang, $name);

        $n = 10;
        while ($n--) {
            $documents = $collection->find(['key' => ['$in' => array_values($ids)]]);
            $found     = [];
            foreach ($documents as $document) {
                $found[$document['key']] = $document['key'];
                parent::set($lang, $name, $document['key'], $document['data']);
            }
            $remain = array_diff_key($this->prepared[$key], $this->cached[$key]);
            if (empty($remain) || count($ids) == count($remain) + count($found)) {
                break;
            }
            else {
                $ids = $remain;
            }
        }

        $this->prepared[$key] = $remain;
        parent::loadFromFallback($lang, $name, $fallback, $this->prepared[$key]);
    }

    /**
     * 
     * @param string $lang
     * @param string $name
     * @return MongoCollection
     */
    public function getCollection($lang, $name) {
        $key = $this->getKey($lang, $name);
        if (!isset($this->collections[$key])) {
            $collectionName = $this->collectionPrefix . $key;
            $collection     = $this->mongoDB->selectCollection($collectionName);
            $collection->createIndex(['key' => 1], ['unique' => true]);
            if (isset($this->indexes[$name])) {
                foreach ($this->indexes[$name] as $index) {
                    $collection->createIndex([$index => 1]);
                }
            }
            $this->collections[$key] = $collection;
        }
        return $this->collections[$key];
    }

    /**
     * 
     * @return MongoDatabase
     */
    function getMongoDB() {
        return $this->mongoDB;
    }

}
