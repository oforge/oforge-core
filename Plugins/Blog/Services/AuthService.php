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
class AuthService extends AbstractDatabaseAccess {
    /** @var array|null $userData */
    private $userData = null;
    /** @var User|null $userData */
    private $user = null;

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(User::class);
    }

    public function login() {
        //TODO
    }

    public function registration() {
        //TODO
    }

    /**
     * @return bool
     */
    public function isUserLoggedIn() : bool {
        $this->initUser();

        return isset($this->user);
    }

    /**
     * @return int|null
     */
    public function getUserID() : ?array {
        $this->initUser();

        return $this->isUserLoggedIn() ? $this->user->getId() : null;
    }

    /**
     * @return array|null
     */
    public function getUserData() : ?array {
        $this->initUser();

        return $this->isUserLoggedIn() ? $this->userData : null;
    }

    /**
     * @return User|null
     */
    public function getUser() : ?User {
        $this->initUser();

        return $this->user;
    }

    /**
     * Check and decode user data of session
     */
    private function initUser() {
        if (is_null($this->userData) && isset($_SESSION['auth'])) {
            try {
                /** @var BasicAuthService $authService */
                $authService = Oforge()->Services()->get('auth');
                $user        = $authService->decode($_SESSION['auth']);
                if (isset($user) && $user['type'] == User::class) {
                    $this->userData = $user;
                    $this->user     = $this->repository()->findOneBy(['id' => $user['id']]);
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
    }

}
