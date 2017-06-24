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
 * @method string  getJournal()
 */
class BackstoryAnswer extends AbstractStoredObject
{

    /**
     *
     * @var BackstoryQuestion
     */
    protected $question = null;

    /**
     * 
     * @return array
     */
    public function getRaces()
    {
        return $this->getData('races', []);
    }

    /**
     * 
     * @return array
     */
    public function getProfessions()
    {
        return $this->getData('professions', []);
    }

    /**
     * 
     * @return string
     */
    public function getQuestionId()
    {
        return $this->getData('question');
    }

    /**
     * 
     * @return BackstoryQuestion
     */
    public function getQuestion()
    {
        $this->checkLoadData();
        return $this->question;
    }

    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['question'])) {
            $this->question = new BackstoryQuestion($this->getEnvironment(), $data['question']);
        }
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    public function getApiName()
    {
        return 'backstoryanswers';
    }
}
