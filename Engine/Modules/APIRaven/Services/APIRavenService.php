<?php

namespace Oforge\Engine\Modules\APIRaven\Services;

use Oforge\Engine\Modules\APIRaven\Exceptions\APIRavenAuthFailedException;
use Oforge\Engine\Modules\APIRaven\Exceptions\APIRavenInvalidAuthMethodException;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Exception;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

/**
 * Class APIRavenService
 *
 * @package Oforge\Engine\Modules\APIRaven\Services
 */
class APIRavenService {
    const AUTH_BASIC = 'BASIC_AUTH';
    const AUTH_OAUTH2 = 'OAUTH2';

    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH  = 'PATCH';

    protected $validMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
        self::METHOD_PATCH,
    ];


    protected $cURL;

    protected $apiUrl;

    protected $username;
    protected $password;
    protected $authMethod;
    protected $authPath;

    /**
     * APIRavenService constructor.
     *
     * @param string $apiUrl
     * @param string $username
     * @param string $password
     * @param string $authMethod
     * @param string $authPath
     *
     * @throws Exception
     */

    public function __construct(string $apiUrl = '', string $username = '', string $password = '', string $authMethod = self::AUTH_BASIC, string $authPath = '') {
        $this->setApiUrl($apiUrl);
        $this->setApiUsername($username);
        $this->setApiPassword($password);
        $this->setAuthPath($authPath);

        //Initializes the cURL instance
        $this->cURL = curl_init();

        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, 'Oforge');
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
        $this->setAuthMethod($authMethod);
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl) : void {
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    /**
     * @param string $username
     */
    public function setApiUsername(string $username) : void {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setApiPassword(string $password) : void {
        $this->password = $password;
    }

    /**
     * @param string $authPath
     */
    public function setAuthPath(string $authPath) : void {
        $this->authPath = $authPath;
    }

    /**
     * @param $authMethod
     *
     * @throws APIRavenInvalidAuthMethodException
     */
    public function setAuthMethod($authMethod) : void {
        $this->authMethod = $authMethod;

        switch ($this->authMethod) {
            case self::AUTH_BASIC:
                curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($this->cURL, CURLOPT_USERPWD, $this->username . ':' . $this->password);
                break;
            case self::AUTH_OAUTH2:
                $this->refreshOauth2Token();
                break;
            default:
                throw new APIRavenInvalidAuthMethodException($authMethod);
        }
    }

    /**
     * @throws Exception
     */
    public function refreshOauth2Token() : void {
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $postData = 'grant_type=client_credentials';

        curl_setopt($this->cURL, CURLOPT_URL, $this->apiUrl . $this->authPath);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($this->cURL);
        $response = json_decode($response, true);
        $token = $response['access_token'];
        $tokenType = $response['token_type'] . ' ';

        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, null);
        curl_setopt($this->cURL, CURLOPT_USERPWD, null);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, null);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, ['Authorization: ' . $tokenType . $token, 'Content-Type: application/json; charset=utf-8']);
    }

    /**
     * @param $path
     * @param string $method
     * @param array $data
     * @param array $params
     *
     * @return array
     * @throws Exception
     */
    public function call($path, $method = self::METHOD_GET, $data = [], $params = []) {
        if (!in_array($method, $this->validMethods)) {
            throw new Exception('Invalid HTTP-Method: ' . $method);
        }

        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $path = rtrim($path, '?') . '?';
            $path .= $queryString;
        }

        $dataString = json_encode($data, JSON_UNESCAPED_SLASHES);
        curl_setopt($this->cURL, CURLOPT_URL, $this->apiUrl . $path . $queryString);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);

        /**
         * Don't touch post body if there is no data present (i.e. paypal will respond with INVALID_REQUEST)
         */
        if (!empty($data)) {
            curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
        }

        $response = curl_exec($this->cURL);
        $response = json_decode($response, true);

        $status = curl_getinfo($this->cURL);

        return [
            'response' => $response,
            'status' => $status,
        ];
    }

    /**
     * @param $path
     * @param array $queryParams
     *
     * @return array
     * @throws InternalErrorException
     */
    public function get($path, $queryParams = []) {
        $response = $this->call($path, self::METHOD_GET, [], $queryParams);

        if($response['status'] === 401 && $this->authMethod === self::AUTH_OAUTH2) {
            $this->refreshOauth2Token();
            $response = $this->call($path, self::METHOD_GET, [], $queryParams);
            if($response['status'] === 401) throw new APIRavenAuthFailedException($this->apiUrl . $path );
        }

        return $response;
    }

    /**
     * @param $path
     * @param array $body
     * @param array $queryParams
     *
     * @return array
     * @throws InternalErrorException
     */
    public function post($path, $body = [], $queryParams = []) {
        $response = $this->call($path, self::METHOD_POST, $body, $queryParams);

        if($response['status'] === 401 && $this->authMethod === self::AUTH_OAUTH2) {
            $this->refreshOauth2Token();
            $response = $this->call($path, self::METHOD_POST, $body, $queryParams);
            if($response['status'] === 401) throw new InternalErrorException(); // TODO: Custom APIRavenExceptions
        }

        return $response;
    }

    /**
     * @param $path
     * @param array $body
     * @param array $queryParams
     *
     * @return array
     * @throws InternalErrorException
     */
    public function put($path, $body = [], $queryParams = []) {
        $response = $this->call($path, self::METHOD_PUT, $body, $queryParams);

        if($response['status'] === 401 && $this->authMethod === self::AUTH_OAUTH2) {
            $this->refreshOauth2Token();
            $response = $this->call($path, self::METHOD_PUT, $body, $queryParams);
            if($response['status'] === 401) throw new InternalErrorException(); // TODO: Custom APIRavenExceptions
        }

        return $response;
    }

    /**
     * @param $path
     * @param array $queryParams
     *
     * @return array
     * @throws InternalErrorException
     */
    public function delete($path, $queryParams = []) {
        $response = $this->call($path, self::METHOD_DELETE, [], $queryParams);

        if($response['status'] === 401 && $this->authMethod === self::AUTH_OAUTH2) {
            $this->refreshOauth2Token();
            $response = $this->call($path, self::METHOD_DELETE, [], $queryParams);
            if($response['status'] === 401) throw new InternalErrorException(); // TODO: Custom APIRavenExceptions
        }

        return $response;
    }
}
