<?php

namespace Oforge\Engine\Modules\Core\Manager;

/**
 * Service to create secure sessions
 * Class SessionManager
 */
class SessionManager
{
    private const SESSION_COOKIE_NAME = 'oforge_session';
    /** @var int $lifetimeSeconds */
    private static $lifetimeSeconds;
    /** @var string $path */
    private static $path;
    /** @var string|null $domain */
    private static $domain;
    /** @var bool|null $secure */
    private static $secure;
    /** @var string $samesite */
    private static $samesite;

    /** Prevent isntance */
    private function __construct()
    {
    }

    /**
     * Start the session
     *
     * @param int $lifetimeSeconds
     * @param string $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param string $samesite
     */
    public static function start(int $lifetimeSeconds = 0, string $path = '/', ?string $domain = null, ?bool $secure = null, string $samesite = 'strict') : void
    {
        $sessionStatus = session_status();

        if ($sessionStatus === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name("oforge_session");
        if ( !empty($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - 180) {
            unset($_COOKIE[self::SESSION_COOKIE_NAME]);
            session_destroy();
        }
        // Set the domain to default to the current domain.
        $domain = $domain ?? $_SERVER['SERVER_NAME'];
        // Set the default secure value to whether the site is being accessed with SSL
        $secure   = $secure ?? isset($_SERVER['HTTPS']);
        $httponly = true;

        self::$lifetimeSeconds = $lifetimeSeconds;
        self::$path            = $path;
        self::$domain          = $domain;
        self::$secure          = $secure;
        self::$samesite        = $samesite;
        // Set the cookie settings and start the session
        if (PHP_VERSION_ID < 70300) {
            if ( !empty($samesite)) {
                $path .= '; samesite=' . $samesite;
            }
            session_set_cookie_params($lifetimeSeconds, $path, $domain, $secure, $httponly);
        } else {
            $params = [
                'lifetime' => $lifetimeSeconds,
                'path'     => $path,
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => $httponly,
            ];
            if ( !empty($samesite)) {
                $params['samesite'] = $samesite;
            }
            session_set_cookie_params($params);
        }
        session_start();
        $_SESSION['created_time'] = time();
    }

    /**
     * Regenerate the session
     */
    public static function regenerate() : void
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $oldSessionData = $_SESSION;
        self::destroy();
        self::start(self::$lifetimeSeconds, self::$path, self::$domain, self::$secure, self::$samesite);
        $_SESSION                 = array_merge($_SESSION, $oldSessionData);
        $_SESSION['created_time'] = time();
    }

    /**
     * Destroy the session an the corresponding cookie
     */
    public static function destroy() : void
    {
        $_SESSION = [];
        unset($_COOKIE[self::SESSION_COOKIE_NAME]);
        session_destroy();
        session_id(session_create_id());
    }

}
