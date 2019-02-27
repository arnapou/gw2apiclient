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
 * @method string getDescription()
 * @method string getTimeline()
 * @method string getLevel()
 * @method string getOrder()
 * @method string getChapters()
 * @method string getRaces()
 */
class Story extends AbstractStoredObject
{
    protected $season;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['season'])) {
            $this->season = new StorySeason($this->getEnvironment(), $data['season']);
        }
    }

    /**
     *
     * @return StorySeason
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->getData('flags', []);
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return \in_array($flag, (array)$this->getFlags());
    }

    public function getApiName()
    {
        return 'stories';
    }
}
