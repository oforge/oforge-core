<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:01
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\ORMException;
use Exception;
use Firebase\JWT\JWT;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class AuthService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class AuthService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' =>  BackendUser::class,
        ]);
    }

    /**
     * Create a JSON Web Token
     *
     * @param array $userData
     *
     * @return string
     */
    public function createJWT(array $userData) : string {
        if (isset($userData['password'])) {
            unset($userData['password']);
        }
        $salt = Oforge()->Settings()->get("jwt_salt");
        return JWT::encode($userData, $salt, 'HS512');
    }

    /**
     * Check, if the current Token is valid and belongs to the requested userID.
     *
     * @param array $userData
     * @param string $jwt
     *
     * @return bool
     */
    public function hasValidToken(array $userData, string $jwt) : bool {
        $salt = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $salt, ['HS512']);

            $array = $decoded;
            if (isset($array['user'])) {
                return $array == $userData;
            }
        } catch (Exception $e) {
            Oforge()->Logger()->get("system")->addWarning($e->getMessage(), ["exception" => $e]);
        }

        return false;
    }

    /**
     * Check if the token is valid (can be decoded)
     *
     * @param string $jwt
     *
     * @return bool
     */
    public function isValid(string $jwt) {
        /**
         * TODO: add more information into the Token for better validation, e.g. created-timestamp
         */
        $decode = $this->decode($jwt);

        return isset($decode);
    }

    /**
     * decode the token
     *
     * @param string $jwt
     *
     * @return array|null
     */
    public function decode(?string $jwt) {
        if (!isset($jwt)) {
            return null;
        }
        $salt = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $salt, ['HS512']);

            return (array) $decoded;
        } catch (Exception $exception) {
            Oforge()->Logger()->get('system')->addWarning($exception->getMessage(), ["exception" => $exception]);
        }

        return null;
    }

    /**
     * create initial user
     *
     * @throws ORMException
     * @throws Exception
     */
    public function createAdmin(string $email, string $name) {

        if ($this->repository()->matching(
            Criteria::create()->where(Criteria::expr()->eq('email', $email))
        )->count() > 0) {
            return "That user already exists";
        }

        /** @var PasswordService $passwordService */
        $passwordService = new PasswordService();
        $password = $passwordService->generatePassword();
        $dbPassword = $passwordService->hash($password);

        $backendUser = new BackendUser();
        $backendUser->setName($name);
        $backendUser->setRole(0);
        $backendUser->setId(1)
            ->setEmail($email)
            ->setPassword($dbPassword)
            ->setActive(true);
        $this->entityManager()->create($backendUser);
        return "Initial user created. Email: " . $email . " Password: " . $password;
    }
}
