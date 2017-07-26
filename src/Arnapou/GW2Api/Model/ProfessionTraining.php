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
 * @method string getId()
 * @method string getCategory()
 * @method string getName()
 */
class ProfessionTraining extends AbstractObject
{

    const CATEGORY_SKILLS                = 'Skills';
    const CATEGORY_SPECIALIZATIONS       = 'Specializations';
    const CATEGORY_ELITE_SPECIALIZATIONS = 'EliteSpecializations';

    /**
     *
     * @var array
     */
    protected $track = [];

    protected function setData($data)
    {
        parent::setData($data);

        $env = $this->getEnvironment();
        if (isset($data['track']) && is_array($data['track'])) {
            foreach ($data['track'] as $item) {
                $this->track[] = new ProfessionTrainingTrackElement($env, $item);
            }
        }
    }

    /**
     *
     * @return integer
     */
    public function getTotalCost()
    {
        $max = 0;
        foreach ($this->getTrack() as $item) {
            $val = $item->getCost();
            if ($val > $max) {
                $max = $val;
            }
        }
        return $max;
    }

    /**
     *
     * @return array
     */
    public function getTrack()
    {
        return $this->track;
    }
}
