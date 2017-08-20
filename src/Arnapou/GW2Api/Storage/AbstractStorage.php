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
use Arnapou\GW2Api\Exception\AllIdsProvidedAreInvalidException;

abstract class AbstractStorage implements StorageInterface
{

    /**
     *
     * @var array
     */
    protected $prepared = [];

    /**
     *
     * @var array
     */
    protected $cached = [];

    /**
     *
     * @var array
     */
    protected $recursivePreparedLogic = [
        'achievementsgroups'     => [['key' => 'categories', 'collection' => 'achievementscategories']],
        'achievementscategories' => [['key' => 'achievements', 'collection' => 'achievements']],
        'titles'                 => [['key' => 'achievement', 'collection' => 'achievements']],
        'finishers'              => [['key' => 'unlock_items', 'collection' => 'items']],
        'minis'                  => [['key' => 'item_id', 'collection' => 'items']],
        'gliders'                => [
            ['key' => 'unlock_items', 'collection' => 'items'],
            ['key' => 'default_dyes', 'collection' => 'colors'],
        ],
        'mailcarriers'           => [
            ['key' => 'unlock_items', 'collection' => 'items'],
        ],
        'backstoryquestions'     => [['key' => 'answers', 'collection' => 'backstoryanswers']],
        'backstoryanswers'       => [['key' => 'question', 'collection' => 'backstoryquestions']],
        'specializations'        => [
            ['key' => 'minor_traits', 'collection' => 'traits'],
            ['key' => 'major_traits', 'collection' => 'traits'],
        ],
        'skills'                 => [
            ['key' => 'flip_skill', 'collection' => 'skills'],
            ['key' => 'toolbelt_skill', 'collection' => 'skills'],
            ['key' => 'prev_chain', 'collection' => 'skills'],
            ['key' => 'next_chain', 'collection' => 'skills'],
            ['key' => 'transform_skills', 'collection' => 'skills'],
            ['key' => 'bundle_skills', 'collection' => 'skills'],
        ],
        'items'                  => [
            ['key' => 'default_skin', 'collection' => 'skins'],
            ['key' => 'suffix_item_id', 'collection' => 'items'],
        ],
    ];

    public function clearCache()
    {
        $this->cached = [];
    }

    public function getKey($lang, $name)
    {
        return $lang . '_' . $name;
    }

    public function prepare($lang, $name, $id)
    {
        $key = $this->getKey($lang, $name);
        if ($id && !isset($this->cached[$key], $this->cached[$key][$id])) {
            $this->prepared[$key][$id] = (string)$id;
        }
    }

    public function get($lang, $name, $id, $fallback)
    {
        $key = $this->getKey($lang, $name);
        if (isset($this->cached[$key], $this->cached[$key][$id])) {
            return $this->cached[$key][$id];
        } elseif ($this->loadPrepared($lang, $name, $fallback)) {
            if (isset($this->cached[$key], $this->cached[$key][$id])) {
                return $this->cached[$key][$id];
            }
        }
        return null;
    }

    public function set($lang, $name, $id, $data)
    {
        $this->cached[$this->getKey($lang, $name)][$id] = $data;

        if (isset($this->recursivePreparedLogic[$name])) {
            foreach ($this->recursivePreparedLogic[$name] as $logic) {
                if (isset($data[$logic['key']])) {
                    if (is_array($data[$logic['key']])) {
                        foreach ($data[$logic['key']] as $itemid) {
                            $this->prepare($lang, $logic['collection'], $itemid);
                        }
                    } else {
                        $this->prepare($lang, $logic['collection'], $data[$logic['key']]);
                    }
                }
            }
        }
    }

    protected function loadPrepared($lang, $name, $fallback)
    {
        $key = $this->getKey($lang, $name);
        if (!empty($this->prepared[$key])) {
            if (!isset($this->cached[$key])) {
                $this->cached[$key] = [];
            }
            $this->loadFromFallback($lang, $name, $fallback, $this->prepared[$key]);
            $this->prepared[$key] = array_diff_key($this->prepared[$key], $this->cached[$key]);
            return true;
        }
        return false;
    }

    protected function loadFromFallback($lang, $name, $fallback, $ids)
    {
        if ($fallback && is_callable($fallback) && !empty($ids)) {
            try {
                $items = call_user_func($fallback, array_values($ids));
            } catch (AllIdsProvidedAreInvalidException $ex) {
                $items = [];
            } catch (\Exception $ex) {
                $items = null;
            }
            if (is_array($items)) {
                $receivedIds = [];
                foreach ($items as $item) {
                    if (isset($item['id'])) {
                        $receivedIds[] = $item['id'];
                        $this->set($lang, $name, $item['id'], $item);
                    }
                }
                $notReceivedIds = array_diff($ids, $receivedIds);
                foreach ($notReceivedIds as $id) {
                    $this->set($lang, $name, $id, ['id' => $id, '_empty_' => 1]);
                }
            }
        }
    }
}
