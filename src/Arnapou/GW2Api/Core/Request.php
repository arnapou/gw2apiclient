<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Core;

use Arnapou\GW2Api\Event\Event;
use Arnapou\GW2Api\Exception\RequestException;

class Request implements RequestInterface {

    /**
     *
     * @var RequestManager
     */
    protected $manager;

    /**
     *
     * @var integer
     */
    protected $url;

    /**
     *
     * @var array
     */
    protected $parameters;

    /**
     *
     * @var array
     */
    protected $headers;

    /**
     * 
     */
    public function __construct(RequestManager $manager, $url, $parameters = [], $headers = []) {
        $this->manager    = $manager;
        $this->url        = $url;
        $this->parameters = $parameters;
        $this->headers    = $headers;
    }

    /**
     * 
     * @param string $param
     * @return integer
     */
    public function getParameter($param) {
        if (isset($this->parameters[$param])) {
            return $this->parameters[$param];
        }
        return null;
    }

    /**
     * 
     * @param string $param
     * @param string $value
     * @return Request
     */
    public function setParameter($param, $value) {
        $this->parameters[$param] = $value;
        return $this;
    }

    /**
     * 
     * @return Response
     */
    public function execute($cacheRetention = null) {
        $url        = $this->url;
        $parameters = $this->parameters;
        $headers    = $this->headers;
        $cache      = $this->manager->getCache();

        $requestUrl = \Arnapou\GW2Api\url_append($url, $parameters);

        if ($cacheRetention === null) {
            $cacheRetention = $this->manager->getCacheRetention($url);
        }
        if ($cacheRetention < 0) {
            $cacheRetention = 0;
        }

        // try to retrieve from cache
        $cacheKey = $requestUrl;
        if ($cache && $cacheRetention > 0) {
            $cached = $cache->get($cacheKey);
            if ($cached !== null) {
                return new Response($this, $cached['headers'], $cached['data']);
            }
        }

        $tries = 10;
        while (true) {

            $time = microtime(true);

            $curl = new Curl();
            $curl->setUrl($requestUrl);
            $curl->setUserAgent($this->manager->getCurlUserAgent());
            $curl->setTimeout($this->manager->getCurlRequestTimeout());
            $curl->setHeaders($headers);
            $curl->setGet();

            $response        = new CurlResponse($curl);
            $responseHeaders = $response->getHeaders();

            if ($response->getErrorCode()) {
                throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
            }

            if ($response->getInfoHttpCode() == 503) {
                usleep(100000); // 100 ms
                if ($tries-- == 0) {
                    throw new RequestException('HTTP Error 503. The service is unavailable.');
                }
                continue;
            }

            $event         = new Event();
            $event['uri']  = $requestUrl;
            $event['time'] = microtime(true) - $time;
            $this->manager->getEventListener()->trigger(RequestManager::onRequest, $event);

            break;
        }

        $data = \Arnapou\GW2Api\json_decode($response->getContent());

        // store in cache if needed
        if ($cache && $cacheRetention > 0) {
            $cache->set($cacheKey, [
                'headers' => $responseHeaders,
                'data'    => $data,
                ], $cacheRetention);
        }
        return new Response($this, $responseHeaders, $data);
    }

    /**
     * 
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

}
