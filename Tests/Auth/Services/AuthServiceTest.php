<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 15:37
 */

declare(strict_types=1);

namespace Tests\Auth\Services;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Tests\TestCase;

final class AuthServiceTest extends TestCase
{
    /**
     * @var $key string
     */
    protected $key;
    
    /**
     * @var $authService AuthService
     */
    protected $authService;
    
    protected function setUp() {
        $this->key = Oforge()->Settings()->get("jwt_salt");
        $this->authService = Oforge()->Services()->get("auth");
    }
    
    public function testCanCreateJsonWebToken(): void
    {
        $data = $this->authService->createJWT(["test"]);
        $this->assertInternalType('string', $data);
    }
}