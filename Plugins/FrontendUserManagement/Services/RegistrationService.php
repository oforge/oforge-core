<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use Insertion\Models\InsertionProfile;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Slim\Router;

class RegistrationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => User::class]);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array|null
     * @throws ORMException
     */
    public function register(string $email, string $password) {
        $user = null;

        if (!$this->userExists($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);
            $this->entityManager()->create($user);
            $userDetails = UserDetail::create(['user' => $user]);
            $userAddress = UserAddress::create(['user' => $user]);
            $userProfile = InsertionProfile::create(['user' => $user]);
            $this->entityManager()->create($userProfile);
            $this->entityManager()->create($userAddress);
            $this->entityManager()->create($userDetails);

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }

        return $user;
    }

    /**
     * @param array $user
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unregister(array $user) {
        $user = $this->repository()->findOneBy(['email' => $user['email']]);
        $this->entityManager()->remove($user);
    }

    /**
     * @param array $user
     *
     * @return string
     * @throws ORMException
     */
    public function generateActivationLink(array $user) :string {
        /** @var Router $router */
        $activationLink = 'http://';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $activationLink = 'https://';
        }
        $activationLink .= $_SERVER['HTTP_HOST'];
        $router         = Oforge()->App()->getContainer()->get("router");
        $user           = $this->repository()->findOneBy(['email' => $user['email']]);
        $activationLink .= $router->pathFor('frontend_registration_activate') . '?activate=' . $user->getGuid();

        return $activationLink;
    }

    /**
     * @param string $guid
     *
     * @return mixed
     * @throws ORMException
     */
    public function activate(string $guid) {
        /** @var User $user */
        $user = $this->repository()->findOneBy(['guid' => $guid]);
        if (isset($user)) {
            $user->setActive(true);
            $this->entityManager()->update($user);

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return bool
     * @throws ORMException
     */
    public function userExists(string $email) {
        return $this->repository()->findOneBy(['email' => $email]) !== null;
    }

    /**
     * @param string $email
     *
     * @return object|null
     * @throws ORMException
     */
    public function getUser(string $email) {
        return $this->repository()->findOneBy(['email' => $email]);
    }

    /**
     * @param string $guid
     *
     * @return bool
     * @throws ORMException
     */
    public function userIsActive(string $guid) {
        /** @var User $user */
        $user = $this->repository()->findOneBy(['guid' => $guid]);
        if (isset($user)) {
            return $user->isActive();
        }

        return false;
    }
}
