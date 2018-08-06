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
 * @method string getName()
 */
class PvpSeason extends AbstractStoredObject
{
    protected $divisions = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['divisions']) && is_array($data['divisions'])) {
            $env = $this->getEnvironment();
            foreach ($data['divisions'] as $item) {
                $this->divisions[] = new PvpDivision($env, $item);
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getDivisions()
    {
        return $this->divisions;
    }

    /**
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getData('active') ? true : false;
    }

    /**
     *
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getDateStart()
    {
        $date = $this->getData('start');
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    /**
     *
     * @return string YYYY-MM-DD HH:MM UTC format
     */
    public function getDateEnd()
    {
        $date = $this->getData('end');
        return $date ? gmdate('Y-m-d H:i', strtotime($date)) : null;
    }

    public function getApiName()
    {
        return 'pvpseasons';
    }
}
