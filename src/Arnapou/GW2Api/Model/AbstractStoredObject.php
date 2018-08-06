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

use Arnapou\GW2Api\Environment;

abstract class AbstractStoredObject extends AbstractObject
{
    protected $objectId;
    protected $objectLoaded  = false;
    protected $clientVersion = 2;

    public function __construct(Environment $environment, $id)
    {
        parent::__construct($environment, null);

        $this->objectId = $id;
        if ($this->objectId) {
            $environment->getStorage()->prepare($this->getEnvironment()->getLang(), $this->getApiName(), $this->objectId);
        }
    }

    public function getId()
    {
        return $this->objectId;
    }

    protected function checkLoadData()
    {
        if (!$this->objectLoaded) {
            if ($this->objectId) {
                $env     = $this->getEnvironment();
                $storage = $env->getStorage();
                $client  = call_user_func([$env, 'getClientVersion' . $this->clientVersion]);
                $method  = $this->getApiMethod();
                $data    = $storage->get($env->getLang(), $this->getApiName(), $this->objectId, [$client, $method]);
                $this->setData($data);
            }
            $this->objectLoaded = true;
        }
    }

    public function getData($keys = null, $default = null, $array = null)
    {
        $this->checkLoadData();
        return parent::getData($keys, $default, $array);
    }

    /**
     *
     * @return string
     */
    public function getApiMethod()
    {
        return 'api' . ucfirst($this->getApiName());
    }

    /**
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->objectId) || $this->getData('_empty_') == 1;
    }

    abstract public function getApiName();
}
