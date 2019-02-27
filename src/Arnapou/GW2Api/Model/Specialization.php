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
 * @doc https://wiki.guildwars2.com/wiki/API:2/traits
 *
 * @method getBackground()
 * @method getIcon()
 * @method getName()
 * @method getProfession()
 * @method isElite()
 */
class Specialization extends AbstractStoredObject
{
    /**
     *
     * @var array
     */
    protected $minorTraits = [];

    /**
     *
     * @var array
     */
    protected $majorTraits = [];

    /**
     *
     * @return string
     */
    public function getMinorTraitsIds()
    {
        return $this->getData('minor_traits');
    }

    /**
     *
     * @return array
     */
    public function getMinorTraits()
    {
        $this->checkLoadData();
        return $this->minorTraits;
    }

    /**
     *
     * @return string
     */
    public function getMajorTraitsIds()
    {
        return $this->getData('major_traits');
    }

    /**
     *
     * @return array
     */
    public function getMajorTraits()
    {
        $this->checkLoadData();
        return $this->majorTraits;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['minor_traits']) && \is_array($data['minor_traits'])) {
            foreach ($data['minor_traits'] as $id) {
                $this->minorTraits[] = new SpecializationTrait($this->getEnvironment(), $id);
            }
        }
        if (isset($data['major_traits']) && \is_array($data['major_traits'])) {
            foreach ($data['major_traits'] as $id) {
                $this->majorTraits[] = new SpecializationTrait($this->getEnvironment(), $id);
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
        return 'specializations';
    }
}
