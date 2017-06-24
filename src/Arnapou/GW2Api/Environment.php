<?php
/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Arnapou\GW2Api;

use Arnapou\GW2Api\Cache\CacheInterface;
use Arnapou\GW2Api\Exception\Exception;
use Arnapou\GW2Api\Storage\StorageInterface;

/**
 *
 */
class Environment
{

    // EVENTS
    const onRequest = 'onRequest';
    // LANGS
    const LANG_DE   = 'de';
    const LANG_EN   = 'en';
    const LANG_ES   = 'es';
    const LANG_FR   = 'fr';
    const LANG_KO   = 'ko';
    const LANG_ZH   = 'zh';

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     *
     * @var array
     */
    protected $cacheRetentionRules = [];

    /**
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     *
     * @var integer
     */
    protected $cacheRetention = 300; // 5 minutes

    /**
     *
     * @var string
     */
    protected $lang;

    /**
     *
     * @var string
     */
    protected $accessToken;

    /**
     *
     * @var int
     */
    protected $curlRequestTimeout = 10;

    /**
     *
     * @var int
     */
    protected $requestRetries = 10;

    /**
     *
     * @var int
     */
    protected $requestRetryDelay = 100; // ms

    /**
     *
     * @var string
     */
    protected $curlUserAgent = 'PHP Arnapou GW2 Api Client';

    /**
     *
     * @var Core\ClientVersion1
     */
    protected $clientVersion1;

    /**
     *
     * @var Core\ClientVersion2
     */
    protected $clientVersion2;

    /**
     *
     * @var boolean
     */
    protected $useSmartCaching = false;

    /**
     * 
     * @param string $lang
     */
    public function __construct($lang = self::LANG_EN)
    {
        $this->setLang($lang);
        $this->eventListener = new Event\EventListener();
    }

    /**
     * 
     * @param string $pattern
     * @param integer $seconds
     * @return Environment
     */
    public function addCacheRetentionRule($pattern, $seconds)
    {
        if ($seconds <= 1) {
            throw new Exception('Retention cannot be lower than 2 seconds');
        }
        $this->cacheRetentionRules[$pattern] = $seconds;
        return $this;
    }

    /**
     * 
     * @param Core\ClientVersion1 $client
     * @return Environment
     */
    public function setClientVersion1(Core\ClientVersion1 $client)
    {
        $this->clientVersion1 = $client;
        return $this;
    }

    /**
     * 
     * @return Core\ClientVersion1
     */
    public function getClientVersion1()
    {
        if (empty($this->clientVersion1)) {
            $this->clientVersion1 = new Core\ClientVersion1($this);
        }
        return $this->clientVersion1;
    }

    /**
     * 
     * @param Core\ClientVersion2 $client
     * @return Environment
     */
    public function setClientVersion2(Core\ClientVersion2 $client)
    {
        $this->clientVersion2 = $client;
        return $this;
    }

    /**
     * 
     * @return Core\ClientVersion2
     */
    public function getClientVersion2()
    {
        if (empty($this->clientVersion2)) {
            $this->clientVersion2 = new Core\ClientVersion2($this);
        }
        return $this->clientVersion2;
    }

    /**
     * 
     * @param CacheInterface $cache
     * @return Environment
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * 
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 
     * @param StorageInterface $storage
     * @return Environment
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * 
     * @return StorageInterface
     */
    public function getStorage()
    {
        if (empty($this->storage)) {
            $this->storage = new Storage\RuntimeStorage();
        }
        return $this->storage;
    }

    /**
     * 
     * @return boolean
     */
    function getUseSmartCaching()
    {
        return $this->useSmartCaching;
    }

    /**
     * 
     * @param boolean $bool
     * @return Environment
     */
    function setUseSmartCaching($bool)
    {
        $this->useSmartCaching = $bool ? true : false;
        return $this;
    }

    /**
     * 
     * @param string $lang
     * @return Environment
     */
    public function setLang($lang)
    {
        if (!in_array($lang, [self::LANG_DE, self::LANG_EN, self::LANG_ES, self::LANG_FR, self::LANG_KO, self::LANG_ZH])) {
            throw new Exception('Wrong lang parameter.');
        }
        $this->lang = $lang;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * 
     * @param string $token
     * @return Environment
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * 
     * @param integer $seconds default cache retention
     * @return Environment
     */
    public function setCacheRetention($seconds)
    {
        if ($seconds <= 1) {
            throw new Exception('Retention cannot be lower than 2 seconds');
        }
        $this->cacheRetention = $seconds;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getCacheRetention($url = null)
    {
        if ($url) {
            foreach ($this->cacheRetentionRules as $pattern => $retention) {
                if (strpos($url, $pattern) !== false) {
                    return $retention;
                }
            }
        }
        return $this->cacheRetention;
    }

    /**
     * 
     * @param string $userAgent
     * @return Environment
     */
    public function setCurlUserAgent($userAgent)
    {
        $this->curlUserAgent = $userAgent;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getCurlUserAgent()
    {
        return $this->curlUserAgent;
    }

    /**
     * 
     * @param integer $seconds
     * @return Environment
     */
    public function setCurlRequestTimeout($seconds)
    {
        $this->curlRequestTimeout = $seconds;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getCurlRequestTimeout()
    {
        return $this->curlRequestTimeout;
    }

    /**
     * 
     * @param integer $retries
     * @return Environment
     */
    public function setRequestRetries($retries)
    {
        $this->requestRetries = $retries;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getRequestRetries()
    {
        return $this->requestRetries;
    }

    /**
     * 
     * @param integer $delay cannot be lower than 20 ms
     * @return Environment
     */
    public function setRequestRetryDelay($delay)
    {
        if ($delay < 20) {
            $delay = 20;
        }
        $this->requestRetryDelay = $delay;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getRequestRetryDelay()
    {
        return $this->requestRetryDelay;
    }

    /**
     * 
     * @return Event\EventListener
     */
    public function getEventListener()
    {
        return $this->eventListener;
    }
}
