<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 10.12.2018
 * Time: 10:40
 */

declare(strict_types=1);

namespace Tests\A_PreCheck;

use Oforge\Engine\Tests\TestCase;

/**
 * Just to check if phpunit is running correctly
 * Class A_PreCheckTest
 * @package Tests\A_PreCheck
 */
final class A_PreCheckTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testCheckIfFailedTestWorks(): void
    {
        $this->assertSame("test", "test1");
    }
    
    /**
     * @throws \Exception
     */
    public function testCheckIfPassedTestWorks(): void
    {
        $this->assertSame("test", "test");
    }
}