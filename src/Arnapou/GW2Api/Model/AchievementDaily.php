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
 * @method int getId()
 * @method int getRequiredAccess()
 */
class AchievementDaily extends AbstractObject
{
    /**
     *
     * @var array
     */
    protected $categories = [];

    /**
     *
     * @var Achievement
     */
    protected $achievement;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['id'])) {
            $this->achievement = new Achievement($this->getEnvironment(), $data['id']);
        }
    }

    public function __call($name, $arguments)
    {
        $val = parent::__call($name, $arguments);
        if ($val === null && empty($arguments) && $this->achievement) {
            return $this->achievement->$name();
        }
        return $val;
    }

    /**
     *
     * @return Achievement
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    /**
     *
     * @return array
     */
    public function getLevelMin()
    {
        return $this->getData(['level', 'min']);
    }

    /**
     *
     * @return array
     */
    public function getLevelMax()
    {
        return $this->getData(['level', 'max']);
    }

    /**
     *
     * @return array
     */
    public function requireGuildWars2()
    {
        return in_array(Account::GAME_ACCESS_CORE, $this->getRequiredAccess());
    }

    /**
     *
     * @return array
     */
    public function requireHeartOfThorns()
    {
        return in_array(Account::GAME_ACCESS_HOT, $this->getRequiredAccess());
    }
}
