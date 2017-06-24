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

/**
 *
 * @method string getScores()
 * @method string getKills()
 * @method string getDeaths()
 * @method string getMaps()
 */
class WvwMatch extends AbstractStoredObject
{

    protected $worlds    = [];
    protected $allWorlds = [];

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();

        if (isset($data['worlds'])) {
            foreach ($data['worlds'] as $color => $id) {
                $this->worlds[$color] = new World($env, $id);
            }
        }
        if (isset($data['all_worlds'])) {
            foreach ($data['all_worlds'] as $color => $ids) {
                foreach ($ids as $id) {
                    $this->allWorlds[$color][] = new World($env, $id);
                }
            }
        }
    }

    /**
     * 
     * @return array
     */
    public function getWorlds()
    {
        $this->checkLoadData();
        return $this->worlds;
    }

    /**
     * 
     * @return array
     */
    public function getAllWorlds()
    {
        $this->checkLoadData();
        return $this->allWorlds;
    }

    /**
     * 
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getStartTime()
    {
        $date = $this->getData('start_time');
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    /**
     * 
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getEndTime()
    {
        $date = $this->getData('end_time');
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    public function getApiName()
    {
        return 'wvwmatches';
    }
}
