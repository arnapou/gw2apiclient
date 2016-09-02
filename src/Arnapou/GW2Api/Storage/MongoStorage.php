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

class MongoStorage extends AbstractStorage {

    /**
     *
     * @var \MongoDB
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
     * @param \MongoDB $mongoDB
     */
    public function __construct(\MongoDB $mongoDB, $collectionPrefix = 'storage_') {
        $this->mongoDB          = $mongoDB;
        $this->collectionPrefix = $collectionPrefix;
    }

    public function set($lang, $name, $id, $data) {
        $this->getCollection($lang, $name)
            ->update([
                'key' => (string) $id,
                ], [
                'key'         => (string) $id,
                'data'        => $data,
                'datecreated' => new \MongoDate(),
                ], [
                'upsert' => true
        ]);
        parent::set($lang, $name, $id, $data);
    }

    protected function loadPrepared($lang, $name, $fallback) {
        $key = $this->getKey($lang, $name);
        if (!empty($this->prepared[$key])) {
            if (!isset($this->cached[$key])) {
                $this->cached[$key] = [];
            }
            $collection = $this->getCollection($lang, $name);
            $documents  = $collection->find(['key' => ['$in' => array_values($this->prepared[$key])]]);
            foreach ($documents as $document) {
                parent::set($lang, $name, $document['key'], $document['data']);
            }
            $this->loadFromFallback($lang, $name, $fallback, array_diff_key($this->prepared[$key], $this->cached[$key]));
            $this->prepared[$key] = array_diff_key($this->prepared[$key], $this->cached[$key]);
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $lang
     * @param string $name
     * @return \MongoCollection
     */
    public function getCollection($lang, $name) {
        $key = $this->getKey($lang, $name);
        if (!isset($this->collections[$key])) {
            $collectionName = $this->collectionPrefix . $key;
            $collection     = $this->mongoDB->selectCollection($collectionName);
            $collection->ensureIndex(['key' => 1], ['unique' => true]);
            if (isset($this->indexes[$name])) {
                foreach ($this->indexes[$name] as $index) {
                    $collection->ensureIndex([$index => 1]);
                }
            }
            $this->collections[$key] = $collection;
        }
        return $this->collections[$key];
    }

    /**
     * 
     * @return \MongoDB
     */
    function getMongoDB() {
        return $this->mongoDB;
    }

}
