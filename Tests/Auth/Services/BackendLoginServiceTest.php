<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 10.12.2018
 * Time: 12:54
 */

declare(strict_types=1);

namespace Tests\Auth\Services;

use Doctrine\ORM\Tools\SchemaTool;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Models\User\User;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Tests\TestCase;

final class BackendLoginServiceTest extends TestCase
{
    /**
     *
     */
    public function getConnection() {
    }
    
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Exception
     */
    public function testUserCanLogin(): void
    {
        $em = Oforge()->DB()->getManager();
        Oforge()->DB()->getSchemaTool()->dropSchema([$em->getClassMetadata(BackendUser::class)]);
        Oforge()->DB()->initSchema([BackendUser::class]);
        $testData = [
            "email" => "testuser@oforge.com",
            "password" => password_hash("geheim", PASSWORD_BCRYPT),
            "role" => 1];
        $user = BackendUser::create(BackendUser::class, $testData);
        $em->persist($user);
        $em->flush($user);
        
        /**
         * @var $backendLoginService BackendLoginService
         */
        $backendLoginService = Oforge()->Services()->get('backend.login');
        
        $result = $backendLoginService->login("false@oforge.com", "");
        $result2 = $backendLoginService->login("testuser@oforge.com", "geheim");
        //$this->assertInternalType("string", $result);
        $this->assertInternalType("string", $result2);
    }
}