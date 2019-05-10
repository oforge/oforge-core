<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 15:37
 */

declare(strict_types=1);

namespace Tests\Auth\Services;

use Exception;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Tests\TestCase;

final class AuthServiceTest extends TestCase
{
    /**
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function testCanCreateJsonWebToken(): void
    {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get('auth');
        $data = $authService->createJWT(["test"]);
        $this->assertInternalType('string', $data);
    }
    
    /**
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function testCanDecodeJsonWebToken(): void {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get('auth');
        $data = $authService->createJWT(["test"]);
        $JWTData = $authService->decode($data);
        $this->assertSame(["test"], $JWTData);
    }
    
    /**
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function testCanCheckIfTokenIsValid() {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get('auth');
        $data = $authService->createJWT(["test"]);
        $isValid = $authService->isValid($data);
        $this->assertTrue($isValid);
    }
    
    /**
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function testCanCheckIfTokenIsInvalid() {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get('auth');
        $isInvalid = $authService->isValid("invalidToken");
        $this->assertFalse($isInvalid);
    }
}
