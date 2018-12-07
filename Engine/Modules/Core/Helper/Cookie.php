<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 09:03
 */

namespace Oforge\Engine\Modules\Core\Helper;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Cookie
{
    /**
     * Eat the cookie
     *
     * @param Response $response
     * @param string $key
     *
     * @return Response
     */
    public function deleteCookie(Response $response, string $key)
    {
        $cookie = urlencode($key).'='.
                  urlencode('deleted').'; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/; secure; httponly';
        $response = $response->withAddedHeader('Set-Cookie', $cookie);
        return $response;
    }
    
    /**
     * Bake the cookie, but don't eat it!
     *
     * @param Response $response
     * @param string $cookieName
     * @param string $cookieValue
     *
     * @return Response
     * @throws \Exception
     */
    public function addCookie(Response $response, $cookieName, $cookieValue)
    {
        $expirationMinutes = 300;
        $expiry = new \DateTimeImmutable('now + '.$expirationMinutes.'minutes');
        $cookie = urlencode($cookieName).'='.
                  urlencode($cookieValue).'; expires='.$expiry->format(\DateTime::COOKIE).'; Max-Age=' .
                  $expirationMinutes * 60 . '; path=/; secure; httponly';
        $response = $response->withAddedHeader('Set-Cookie', $cookie);
        return $response;
    }
    
    /**
     * @param Request $request
     * @param string $cookieName
     * @return string
     */
    public function getCookieValue(Request $request, $cookieName)
    {
        $cookies = $request->getCookieParams();
        return isset($cookies[$cookieName]) ? $cookies[$cookieName] : null;
    }
}
