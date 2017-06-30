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
 * @method string getOrder()
 */
class StorySeason extends AbstractStoredObject
{

    protected $stories;

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['stories']) && is_array($data['stories'])) {
            foreach ($data['stories'] as $id) {
                $this->stories[] = new Story($this->getEnvironment(), $id);
            }
            usort($this->stories, function (Story $a, Story $b) {
                $na = $a->getOrder();
                $nb = $b->getOrder();
                if ($na = $nb) {
                    return 0;
                }
                return $na > $nb ? 1 : -1;
            });
        }
    }

    /**
     *
     * @return array
     */
    public function getStories()
    {
        return $this->stories;
    }

    public function getApiName()
    {
        return 'storiesseasons';
    }
}
