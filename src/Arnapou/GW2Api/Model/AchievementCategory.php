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
 * @doc https://wiki.guildwars2.com/wiki/API:2/achievements
 * 
 * @method string  getDescription()
 * @method string  getIcon()
 * @method string  getName()
 * @method integer getOrder()
 */
class AchievementCategory extends AbstractStoredObject
{

    protected $achievements = [];

    /**
     * 
     * @return array
     */
    public function getAchievementsIds()
    {
        return $this->getData('achievements');
    }

    /**
     * 
     * @return array
     */
    public function getAchievements()
    {
        $this->checkLoadData();
        return $this->achievements;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['achievements']) && is_array($data['achievements'])) {
            foreach ($data['achievements'] as $id) {
                $this->achievements[] = new Achievement($this->getEnvironment(), $id);
            }
        }
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function getApiName()
    {
        return 'achievementscategories';
    }
}
