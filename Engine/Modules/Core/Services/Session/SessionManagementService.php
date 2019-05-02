<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 12:29
 */

namespace Oforge\Engine\Modules\Core\Services\Session;

/**
 * Service to create secure sessions
 *
 * Class SessionManagementService
 */
class SessionManagementService {
    /**
     * Start the session
     *
     * @param int $lifetimeSeconds
     * @param string $path
     * @param null $domain
     * @param null $secure
     */
    public function sessionStart($lifetimeSeconds = 0, $path = '/', $domain = null, $secure = null)
    {
        $sessionStatus = session_status();
        if ($sessionStatus != PHP_SESSION_ACTIVE) {
             session_name( "oforge_session" );
            if ( ! empty( $_SESSION['deleted_time'] ) &&
                 $_SESSION['deleted_time'] < time() - 180 ) {
                session_destroy();
            }
            // Set the domain to default to the current domain.
            $domain = isset( $domain ) ? $domain : $_SERVER['SERVER_NAME'];
    
            // Set the default secure value to whether the site is being accessed with SSL
            $secure = isset( $secure ) ? $secure : isset( $_SERVER['HTTPS'] ) ? true : false;
    
            // Set the cookie settings and start the session
            session_set_cookie_params( $lifetimeSeconds, $path, $domain, $secure, true );
            session_start();
            $_SESSION['created_time'] = time();
        }
    }
    
    /**
     * Regenerate a session
     */
    public function regenerateSession() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $oldSessionData = $_SESSION;
        $this->sessionDestroy();
        $this->sessionStart(0);
        $_SESSION = array_merge($_SESSION, $oldSessionData);
        $_SESSION['created_time'] = time();
    }
    
    /**
     * Destroy the session an the corresponding cookie
     */
    public function sessionDestroy() {
        $_SESSION = [];
        session_destroy();
        session_id(session_create_id());
    }
}
