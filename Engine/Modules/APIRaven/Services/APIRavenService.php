<?php

namespace Oforge\Engine\Modules\APIRaven\Services;

use Monolog\Formatter\JsonFormatter;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use PhpParser\JsonDecoder;
use Exception;

/**
 * Class APIRavenService
 *
 * @package Oforge\Engine\Modules\APIRaven\Services
 */
class APIRavenService {
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';
    protected $validMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
    ];
    protected $apiUrl;
    protected $cURL;
    protected $jsonDecoder;
    protected $jsonFormatter;

    /**
     * APIRavenService constructor.
     *
     * @param string $apiUrl
     * @param string $username
     * @param string $apiKey
     */

    public function __construct($apiUrl = '', $username = '', $apiKey = '') {
        $this->apiUrl = rtrim($apiUrl, '/') . '/';

        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, 'Oforge');
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);

        $this->jsonDecoder   = new JsonDecoder();
        $this->jsonFormatter = new JsonFormatter();
    }

    /**
     * @param string $apiUrl
     * @param string $username
     * @param string $apiKey
     */
    public function setApi($apiUrl, $username, $apiKey) {
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
    }

    /**
     * @param $url
     * @param string $method
     * @param array $data
     * @param array $params
     *
     * @return array
     * @throws Exception
     */
    public function call($url, $method = self::METHOD_GET, $data = [], $params = []) {
        if (!in_array($method, $this->validMethods)) {
            throw new Exception('Invalid HTTP-Method: ' . $method);
        }
        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = rtrim($url, '?') . '?';
        $url .= $queryString;

        if (!StringHelper::startsWith($url, "http")) {
            $url = $this->apiUrl . $url . $queryString;
        }
        $dataString = $this->jsonFormatter->format($data);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);

        $result = curl_exec($this->cURL);
        return $this->jsonDecoder->decode($result);
    }

    public function get($url, $params = []) {
        return $this->call($url, self::METHOD_GET, [], $params);
    }

    /**
     * Send a raven to your servant!
     *
     * @param $url
     * @param array $data
     * @param array $params
     *
     * @return array
     * @throws Exception
     */
    public function post($url, $data = [], $params = []) {
        return $this->call($url, self::METHOD_POST, $data, $params);
    }

    public function put($url, $data = [], $params = []) {
        return $this->call($url, self::METHOD_PUT, $data, $params);
    }

    public function delete($url, $params = []) {
        return $this->call($url, self::METHOD_DELETE, [], $params);
    }
}