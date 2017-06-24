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

use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Environment;

/**
 *
 */
abstract class AbstractObject
{

    /**
     *
     * @var Environment
     */
    private $environment;

    /**
     *
     * @var array
     */
    private $data = null;

    /**
     *
     * @var array
     */
    private static $UNCAMELED = [];

    /**
     * 
     */
    public function __construct(Environment $environment, $data)
    {
        $this->environment = $environment;
        $this->setData($data);
    }

    /**
     * 
     * @param string $cameled
     * @return string
     */
    private static function uncamelString($cameled)
    {
        if (!isset(self::$UNCAMELED[$cameled])) {
            self::$UNCAMELED[$cameled] = strtolower(preg_replace('!([a-z0-9])([A-Z])!s', '$1_$2', $cameled));
        }
        return self::$UNCAMELED[$cameled];
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'get' && $name !== 'get') {
            $property = self::uncamelString(substr($name, 3));
            return $this->getData($property);
        }
        if (substr($name, 0, 2) === 'is' && $name !== 'is') {
            $property = self::uncamelString(substr($name, 2));
            return $this->getData($property) ? true : false;
        }
        return null;
    }

    /**
     * 
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * 
     * @param array $keys
     * @param mixed $default
     * @param array $array
     * @return mixed
     */
    public function getData($keys = null, $default = null, $array = null)
    {
        $return = $array ? $array : $this->data;
        if ($keys !== null) {
            $keys = (array) $keys;
            foreach ($keys as $key) {
                if (!isset($return[$key])) {
                    return $default;
                }
                $return = $return[$key];
            }
        }
        return $return;
    }

    /**
     * 
     * @param array $data
     */
    protected function setData($data)
    {
        $this->data = $data;
    }
}
