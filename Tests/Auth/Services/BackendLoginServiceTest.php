<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 10.12.2018
 * Time: 12:54
 */

declare(strict_types = 1);

namespace Tests\Auth\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Tests\TestCase;

final class BackendLoginServiceTest extends TestCase {m
    /**
     * @var $em EntityManager
     */
    private $em;

    /**
     * @throws ORMException
     */
    public function getConnection() {
        $this->em = Oforge()->DB()->getEntityManager();
        Oforge()->DB()->getSchemaTool()->dropSchema([$this->em->getClassMetadata(BackendUser::class)]);
        Oforge()->DB()->initModelSchema([BackendUser::class], true);

        $testData = [
            "email"    => "testuser@oforge.com",
            "password" => password_hash("geheim", PASSWORD_BCRYPT),
            "role"     => 1,
        ];
        $user     = BackendUser::create($testData);
        $this->em->persist($user);
        $this->em->flush($user);
    }

    /**
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function testUserCanLogin() : void {
        $this->getConnection();
        /**
         * @var $backendLoginService BackendLoginService
         */
        $backendLoginService = Oforge()->Services()->get('backend.login');
        $result              = $backendLoginService->login("testuser@oforge.com", "geheim");
        $this->assertInternalType("string", $result);
    }

    public function testDeniesLoginWithWrongCredentials() : void {
        $this->getConnection();
        $backendLoginService = Oforge()->Services()->get('backend.login');
        $result              = $backendLoginService->login("testuser@oforge.com", "");
        $this->assertInternalType("null", $result);
    }
}
