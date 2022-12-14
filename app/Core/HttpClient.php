<?php

namespace App\Core;

use \Curl\Curl;

class HttpClient
{
    private $useProxy;

    public function __construct($useProxy = true)
    {
        $this->useProxy = $useProxy;
    }

    public function get($url, $data = [], $headers  = [], $cookies = [])
    {
        return $this->_execute($url, $data, $headers, $cookies);
    }

    public function post($url, $data = [], $headers  = [], $cookies = [])
    {
        return $this->_execute($url, $data, $headers, 'post', $cookies);
    }

    private function _execute($url, $data = [], $headers = [], $type = 'get', $cookies = [])
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36');

        if ($this->useProxy) {
            $curl->setOpt(CURLOPT_PROXY, 'http://zproxy.lum-superproxy.io:22225');
            $curl->setOpt(CURLOPT_PROXYUSERPWD, 'lum-customer-travelcompute-zone-static:ogw2gkwws492');
        }

        if (count($headers) > 0) {
            $curl->setHeaders($headers);
        }

        if (count($cookies) > 0) {
            foreach ($cookies as $key => $val) {
                $curl->setCookie($key, $val);
            }
        }


        if ($type === 'post') {
            $curl->post($url, $data);
        } else {
            $curl->get($url, $data);
        }

        $response = [];
        if ($curl->error) {
            $response = [
                'success' => false,
                'error' => 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n",
            ];
        } else {
            $response = ['success' => true, 'body' => $curl->response];
        }
        // var_dump($curl);
        // exit;
        $curl->close();
        return $response;
    }
}
