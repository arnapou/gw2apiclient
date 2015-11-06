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
class Build extends AbstractObject {

    /**
     *
     * @var array
     */
    protected $specializations;

    /**
     *
     * @var array
     */
    protected $alltraits = [];

    /**
     * 
     * @param SimpleClient $client
     * @param array $data
     */
    public function __construct(SimpleClient $client, $data) {
        parent::__construct($client);

        $this->data = $data;

        // preloads
        foreach ($data as $row) {
            if (isset($row['id'], $row['traits'])) {
                foreach ($row['traits'] as $id) {
                    self::$PRELOADS['traits'][] = $id;
                    $this->alltraits[]          = $id;
                }
                self::$PRELOADS['specializations'][] = $row['id'];
            }
        }
    }

    /**
     * 
     * @return array
     */
    public function getSpecializations() {
        if (!isset($this->specializations)) {
            $this->specializations = [];
            foreach ($this->data as $data) {
                if (isset($data['id'], $data['traits'])) {
                    $spe = new Specialization($this->client, $data['id'], $data['traits']);
                    if ($spe->getName()) {
                        $this->specializations[] = $spe;
                    }
                }
            }
        }
        return $this->specializations;
    }

    /**
     * 
     * @return array
     */
    public function getAllTraitsId() {
        return $this->alltraits;
    }

}
