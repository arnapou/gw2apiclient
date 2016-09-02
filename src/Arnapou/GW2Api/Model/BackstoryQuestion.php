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
 * @method string  getTitle()
 * @method string  getDescription()
 * @method string  getOrder()
 */
class BackstoryQuestion extends AbstractStoredObject {

    /**
     *
     * @var array
     */
    protected $answers = [];

    /**
     * 
     * @return array
     */
    public function getRaces() {
        return $this->getData('races', []);
    }

    /**
     * 
     * @return array
     */
    public function getProfessions() {
        return $this->getData('professions', []);
    }

    /**
     * 
     * @return string
     */
    public function getAnswersIds() {
        return $this->getData('answers');
    }

    /**
     * 
     * @return array
     */
    public function getAnswers() {
        $this->checkLoadData();
        return $this->answers;
    }

    protected function setData($data) {
        parent::setData($data);

        if (isset($data['answers']) && is_array($data['answers'])) {
            foreach ($data['answers'] as $id) {
                $this->answers[] = new BackstoryAnswer($this->getEnvironment(), $id);
            }
        }
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getTitle();
    }

    protected function getApiName() {
        return 'backstoryquestions';
    }

}
