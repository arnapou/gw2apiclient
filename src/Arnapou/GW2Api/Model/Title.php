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
 */
class Title extends AbstractStoredObject
{

    use UnlockTrait;

    protected $achievement = null;

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * 
     * @return integer
     */
    public function getAchievementId()
    {
        return $this->getData('achievement');
    }

    /**
     * 
     * @return Achievement
     */
    public function getAchievement()
    {
        $this->checkLoadData();
        return $this->achievement;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['achievement'])) {
            $this->achievement = new Achievement($this->getEnvironment(), $data['achievement']);
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
        return 'titles';
    }
}
