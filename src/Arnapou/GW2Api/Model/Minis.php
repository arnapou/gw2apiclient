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
use Arnapou\GW2Api\SimpleClient;

/**
 *
 */
class Minis extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $unlocked;

    /**
     *
     * @var array
     */
    protected $allIds;

    /**
     *
     * @var array
     */
    protected $minis;

    /**
     *
     * @var integer
     */
    protected $count;

    /**
     *
     * @var integer
     */
    protected $total;

    /**
     *
     * @var boolean
     */
    static protected $preloadMinisDone = false;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->unlocked = $data;
        $this->allIds   = $this->client->v2_minis();

        if (!self::$preloadMinisDone) {
            self::$PRELOADS['minis'] = $this->allIds;
            self::$preloadMinisDone  = true;
        }
    }

    /**
     * 
     */
    protected function prepareObjects() {
        $this->minis = [];
        $this->count = 0;
        $this->total = 0;
        $itemIds     = [];

        $flippedUnlocked = array_flip($this->unlocked);
        foreach ($this->allIds as $id) {
            $unlocked      = isset($flippedUnlocked[$id]);
            $mini          = new Mini($this->client, $id, $unlocked);
            $itemIds[]     = $mini->getItemId();
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->minis[] = $mini;
        }
        $this->preloadItemIds($itemIds);
        uasort($this->minis, function(Mini $mini1, Mini $mini2) {
            return strcmp($mini1->getOrder(), $mini2->getOrder());
        });
    }

    /**
     * 
     * @return array
     */
    public function getMinis() {
        if (!isset($this->minis)) {
            $this->prepareObjects();
        }
        return $this->minis;
    }

    /**
     * 
     * @return integer
     */
    public function getCount() {
        return count($this->unlocked);
    }

    /**
     * 
     * @return integer
     */
    public function getTotal() {
        if (!isset($this->total)) {
            $this->prepareObjects();
        }
        return $this->total;
    }

}
