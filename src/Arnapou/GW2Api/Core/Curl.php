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

class Curl
{
    /**
     *
     * @var resource
     */
    protected $curl;

    
    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        $this->setFollowLocation();
    }

    
    public function __destruct()
    {
        if ($this->curl) {
            @curl_close($this->curl);
            $this->curl = null;
        }
    }

    /**
     *
     * @param string $url
     * @return Curl
     */
    public function setUrl($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $this;
    }

    /**
     *
     * @param int $port
     * @return Curl
     */
    public function setPort($port)
    {
        curl_setopt($this->curl, CURLOPT_PORT, $port);
        return $this;
    }

    /**
     *
     * @param bool $bool
     * @return Curl
     */
    public function setFollowLocation($bool = true)
    {
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $bool ? 1 : 0);
        return $this;
    }

    /**
     *
     * @param string $ua
     * @return Curl
     */
    public function setUserAgent($ua)
    {
        curl_setopt($this->curl, CURLOPT_USERAGENT, $ua);
        return $this;
    }

    /**
     *
     * @param int $seconds
     * @return Curl
     */
    public function setTimeout($seconds)
    {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $seconds);
        return $this;
    }

    /**
     *
     * @param array $array
     * @return Curl
     */
    public function setHeaders($array)
    {
        if (\Arnapou\GW2Api\is_associative_array($array)) {
            $tmp = [];
            foreach ($array as $key => $value) {
                if ("$value" !== '') {
                    $tmp[] = "$key: $value";
                }
            }
            $array = $tmp;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $array);
        return $this;
    }

    /**
     *
     * @return Curl
     */
    public function setGet()
    {
        curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        return $this;
    }

    /**
     *
     * @return Curl
     */
    public function setMethod($method)
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        return $this;
    }

    /**
     *
     * @param array|string $data
     * @return Curl
     */
    public function setPost($data = null)
    {
        curl_setopt($this->curl, CURLOPT_POST, 1);
        if (!empty($data)) {
            if (\is_array($data)) {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, (string)$data);
            }
        }
        return $this;
    }

    /**
     *
     * @return resource
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     *
     * @return CurlResponse
     */
    public function execute()
    {
        $response   = new CurlResponse($this);
        $this->curl = null;
        return $response;
    }
}
