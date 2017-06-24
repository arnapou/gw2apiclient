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
class Dungeon extends AbstractStoredObject
{

    protected $paths = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['paths']) && is_array($data['paths'])) {
            $env = $this->getEnvironment();
            $i   = 0;
            foreach ($data['paths'] as $path) {
                if (isset($path['id'])) {
                    $i++;
                    $path['number'] = $i;
                    $this->paths[]  = new DungeonPath($env, $path);
                }
            }
        }
    }

    /**
     * 
     * @return array
     */
    public function getPaths()
    {
        $this->checkLoadData();
        return $this->paths;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return \Arnapou\GW2Api\id_to_name($this->getId());
    }

    public function getApiName()
    {
        return 'dungeons';
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
