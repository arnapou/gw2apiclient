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
 * @method string getType()
 * @method string getOverlay()
 * @method string getUnderlay()
 */
class PvpHero extends AbstractStoredObject
{

    /**
     *
     * @var array
     */
    protected $unlocked = [];

    /**
     *
     * @var array
     */
    protected $skins = [];

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['skins']) && is_array($data['skins'])) {
            foreach ($data['skins'] as $item) {
                $skin          = new PvpHeroSkin($this->getEnvironment(), $item);
                $skin->setUnlocked(in_array($item['id'], $this->unlocked));
                $this->skins[] = $skin;
            }
        }
    }

    /**
     * 
     * @param array $unlocked
     */
    public function setUnlocked($unlocked)
    {
        $this->unlocked = $unlocked;
    }

    /**
     * 
     * @return array
     */
    public function getSkins()
    {
        $this->checkLoadData();
        return $this->skins;
    }

    public function getApiName()
    {
        return 'pvpheroes';
    }
}
