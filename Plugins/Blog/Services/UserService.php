<?php

namespace Blog\Services;

use Exception;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Services\AuthService as BasicAuthService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class AuthService
 *
 * @package Blog\Services
 */
class UserService extends AbstractDatabaseAccess {
    /** @var array|null $userData */
    private $userData = null;

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(User::class);
    }

    /**
     * @return bool
     */
    public function isLoggedIn() : bool {
        $this->init();

        return isset($this->userData);
    }

    /**
     * @return int|null
     */
    public function getID() : ?int {
        $this->init();

        return $this->isLoggedIn() ? $this->userData['id'] : null;
    }

    /**
     * @return array|null
     */
    public function getData() : ?array {
        $this->init();

        return $this->isLoggedIn() ? $this->userData : null;
    }

    /**
     * Check and decode user data of session
     */
    private function init() {
        if (is_null($this->userData)) {
            $userData = Oforge()->View()->get('user');
            if (isset($userData)) {
                $this->userData = $userData;
            } elseif (isset($_SESSION['auth'])) {
                try {
                    /** @var BasicAuthService $authService */
                    $authService = Oforge()->Services()->get('auth');
                    $user        = $authService->decode($_SESSION['auth']);
                    if (isset($user) && $user['type'] == User::class) {
                        $this->userData = $user;
                    }
                } catch (Exception $exception) {
                    Oforge()->Logger()->logException($exception);
                }
            }
        }
    }

}
