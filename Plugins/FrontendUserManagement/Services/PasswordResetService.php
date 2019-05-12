<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\SessionHelper;
use Slim\Router;

class PasswordResetService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => User::class]);
    }

    public function emailExists(string $email) {
        return $this->repository()->findOneBy(['email' => $email]) !== null;
    }

    /**
     * @param string $email
     *
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createPasswordResetLink(string $email) {
        /** @var Router $router */
        /** @var User $user */
        $user = $this->repository()->findOneBy(['email' => $email]);
        $guid= SessionHelper::generateGuid();
        $user->setGuid($guid);
        $this->entityManager()->flush();

        $activationLink = 'http://';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $activationLink = 'https://';
        }

        $activationLink .= $_SERVER['HTTP_HOST'];
        $router         = Oforge()->App()->getContainer()->get("router");
        $activationLink .= $router->pathFor('frontend_forgot_password_reset') . '?reset=' . $guid;
        return $activationLink;
    }

    /**
     * Check if the sent guid can be found in the users table and if the updated_at field is not older than one day.
     * @param string $guid
     *
     * @return bool
     */
    public function isResetLinkValid(string $guid) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $query = $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->where($queryBuilder->expr()->eq('u.guid', '?1'))
            ->andWhere("CURRENT_TIMESTAMP() < DATE_ADD(u.updatedAt, 1, 'DAY')")
            ->setParameters([1 => $guid])
            ->getQuery();

        $user = $query->execute();
        return !empty($user);
    }

    /**
     * @param string $guid
     * @param string $password
     *
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changePassword(string $guid, string $password) {

        /** @var User $user */
        $user = $this->repository()->findOneBy(['guid' => $guid]);

        if ($user) {
            $user->setGuid(SessionHelper::generateGuid());
            $user->setPassword($password);
            $this->entityManager()->flush();

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }

        return $user;
    }
}
