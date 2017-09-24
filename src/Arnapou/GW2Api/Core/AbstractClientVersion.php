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

use Arnapou\GW2Api\Environment;
use Arnapou\GW2Api\Event\Event;
use Arnapou\GW2Api\Exception\AllIdsProvidedAreInvalidException;
use Arnapou\GW2Api\Exception\ApiUnavailableException;
use Arnapou\GW2Api\Exception\RequestException;

abstract class AbstractClientVersion
{

    const API_FLAG_DISABLED            = 'disabled';
    const API_FLAG_LOCALE_AWARE        = 'locale-aware';
    const API_FLAG_REQUIRE_AUTH        = 'require-auth';
    const API_FLAG_PHP_IMPLEMENTED     = 'php-implemented';
    const API_FLAG_PHP_NOT_IMPLEMENTED = 'php-not-implemented';

    /**
     *
     * @var Environment
     */
    private $environment;

    /**
     *
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     *
     * @param array $parameters
     */
    protected function checkParameters(&$parameters)
    {
        if (!isset($parameters['lang']) && !empty($this->lang)) {
            $parameters['lang'] = $this->lang;
        }
    }

    /**
     *
     * @param string $url
     * @param array  $headers
     * @return Curl
     */
    protected function createCurl($url, $headers)
    {
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setUserAgent($this->getEnvironment()->getCurlUserAgent());
        $curl->setTimeout($this->getEnvironment()->getCurlRequestTimeout());
        $curl->setHeaders($headers);
        $curl->setGet();
        return $curl;
    }

    /**
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     * @return array
     */
    protected function request($url, $parameters = [], $headers = [])
    {
        $environment = $this->getEnvironment();
        if (!isset($parameters['lang'])) {
            $parameters['lang'] = $environment->getLang();
        }

        $requestUrl = \Arnapou\GW2Api\url_append($this->getBaseUrl() . $url, $parameters);

        $cache    = $environment->getCache();
        $cacheKey = $requestUrl;
        if ($cache) {
            $cached = $cache->get($cacheKey);
            if ($cached !== null && isset($cached['data'])) {
                return $cached['data'];
            }
        }

        $retries = $environment->getRequestRetries();
        while (true) {

            $time = microtime(true);

            $curl = $this->createCurl($requestUrl, $headers);

            $response        = $curl->execute();
            $responseHeaders = $response->getHeaders();

            if ($response->getErrorCode()) {
                throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
            }

            $httpCode = $response->getInfoHttpCode();
            if ($httpCode == 503) {
                usleep($this->getEnvironment()->getRequestRetryDelay());
                if ($retries-- <= 0) {
                    throw new ApiUnavailableException('HTTP Error 503. The GW2 API is unreachable.');
                }
                continue;
            } elseif ($httpCode == 404 && stripos($response->getContent(), '"all ids provided are invalid"')) {
                throw new AllIdsProvidedAreInvalidException();
            } elseif ($httpCode != 200 && $httpCode != 206) {
                throw new RequestException(
                    "HTTP Error " . $httpCode . ".\n"
                    . "URL = " . $requestUrl . ".\n"
                    . "Content = " . $response->getContent()
                );
            }

            $data = \Arnapou\GW2Api\json_decode($response->getContent());

            $this->getEnvironment()->getEventListener()->trigger(Environment::onRequest, new Event([
                'code' => $httpCode,
                'url'  => $requestUrl,
                'time' => microtime(true) - $time,
            ]));

            break;
        }

        // store in cache if needed
        if ($cache) {
            $cache->set($cacheKey, [
                'headers' => $responseHeaders,
                'data'    => $data,
            ], $this->getEnvironment()->getCacheRetention($this->getBaseUrl() . $url)
            );
        }

        return $data;
    }

    /**
     * @param array $flags
     * @return array
     * @throws RequestException
     */
    public function getApiList($flags = [])
    {
        $url   = preg_replace('!/$!', '', $this->getBaseUrl());
        $curl  = $this->createCurl($url, []);
        $flags = (array)$flags;

        $response = $curl->execute();
        if ($response->getErrorCode()) {
            throw new RequestException($response->getErrorTitle() . ': ' . $response->getErrorDetail(), $response->getErrorCode());
        }

        $content = $response->getContent();
        if (preg_match('!The following paths[^\n]+((?:\n\s+/v[0-9]/[^\n]+)+)[\n\r\s]*\nKey!si', $content, $m)) {
            $lines = explode("\n", trim($m[1]));
            $lines = array_map('trim', $lines);
            $apis  = [];
            foreach ($lines as $line) {
                $api = null;
                if (preg_match('!^/v[0-9]/([^\s]+)(?:\s+\[(.+)\])$!', $line, $m)) {
                    $api = [
                        'url'   => $m[1],
                        'flags' => $this->getApiFlags($m[1], $m[2]),
                    ];
                } elseif (preg_match('!^/v[0-9]/(.+)$!', $line, $m)) {
                    $api = [
                        'url'   => $m[1],
                        'flags' => $this->getApiFlags($m[1], ''),
                    ];
                }
                if ($api) {
                    if (!empty($flags)) {
                        $flagFound = false;
                        foreach ($api['flags'] as $flag) {
                            if (in_array($flag, $flags)) {
                                $flagFound = true;
                            }
                        }
                    } else {
                        $flagFound = true;
                    }
                    if ($flagFound) {
                        $apis[] = $api;
                    }
                }
            }
            return $apis;
        }

        throw new RequestException('Unable to detect apis list');
    }

    /**
     * @param $apiUrl
     * @param $apiLetters
     * @return array
     */
    protected function getApiFlags($apiUrl, $apiLetters)
    {
        $letters = array_map('trim', array_map('strtolower', explode(',', $apiLetters)));
        $flags   = [];
        if (in_array('l', $letters)) {
            $flags[] = self::API_FLAG_LOCALE_AWARE;
        }
        if (in_array('d', $letters)) {
            $flags[] = self::API_FLAG_DISABLED;
        }
        if (in_array('a', $letters)) {
            $flags[] = self::API_FLAG_REQUIRE_AUTH;
        }
        if (strpos($apiUrl, ':') === false) {
            $method = 'api' . str_replace('/', '', $apiUrl);
            if (method_exists($this, $method)) {
                $flags[] = self::API_FLAG_PHP_IMPLEMENTED;
            } else {
                $flags[] = self::API_FLAG_PHP_NOT_IMPLEMENTED;
            }
        }
        return $flags;
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
     * @return string base url with ending slash
     */
    abstract public function getBaseUrl();
}
