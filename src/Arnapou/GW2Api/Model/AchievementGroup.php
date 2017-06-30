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
 * @method string  getOrder()
 */
class AchievementGroup extends AbstractStoredObject
{

    protected $categories = [];

    /**
     *
     * @return array
     */
    public function getCategoriesIds()
    {
        return $this->getData('categories');
    }

    /**
     *
     * @return array
     */
    public function getCategories()
    {
        $this->checkLoadData();
        return $this->categories;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $id) {
                $this->categories[$id] = new AchievementCategory($this->getEnvironment(), $id);
            }
            uasort($this->categories, function ($a, $b) {
                $ia = $a->getOrder();
                $ib = $b->getOrder();
                if ($ia == $ib) {
                    return strcasecmp($a->getName(), $b->getName());
                }
                return $ia < $ib ? -1 : 1;
            });
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
        return 'achievementsgroups';
    }
}
