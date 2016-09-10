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
 * @method string getRequirement()
 * @method string getOrder()
 * @method string getBackground()
 * @method string getRegion()
 */
class Mastery extends AbstractStoredObject {

    const REGION_MAGUUMA = 'Maguuma';
    const REGION_TYRIA   = 'Tyria';

    protected $levels;

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['levels']) && is_array($data['levels'])) {
            $env = $this->getEnvironment();
            foreach ($data['levels'] as $item) {
                $this->levels[] = new MasteryLevel($env, $item);
            }
        }
    }

    /**
     * 
     * @return array
     */
    public function getLevels() {
        return $this->level;
    }

    public function getApiName() {
        return 'masteries';
    }

}
