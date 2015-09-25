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
class Dyes extends AbstractObject {

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
    protected $colors;

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
    static protected $preloadColorDone = false;

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->unlocked = $data;
        $this->allIds   = $this->client->v2_colors();

        if (!self::$preloadColorDone) {
            self::$PRELOADS['colors'] = $this->allIds;
            self::$preloadColorDone   = true;
        }
    }

    /**
     * 
     */
    protected function prepareObjects() {
        $this->colors = [];
        $this->count  = 0;
        $this->total  = 0;

        $flippedUnlocked = array_flip($this->unlocked);
        foreach ($this->allIds as $id) {
            $unlocked       = isset($flippedUnlocked[$id]);
            $color          = new Color($this->client, $id, $unlocked);
            $this->count += $unlocked ? 1 : 0;
            $this->total++;
            $this->colors[] = $color;
        }
        uasort($this->colors, function(Color $color1, Color $color2) {
            return strcmp($color1->getName(), $color2->getName());
        });
    }

    /**
     * 
     * @return array
     */
    public function getColors() {
        if (!isset($this->colors)) {
            $this->prepareObjects();
        }
        return $this->colors;
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
