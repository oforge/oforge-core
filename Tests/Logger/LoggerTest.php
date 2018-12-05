<?php
declare(strict_types=1);

namespace Tests\Logger;

use Oforge\Engine\Tests\TestCase;

final class LoggerTest extends TestCase
{
    public function testFirst(): void
    {
        $this->assertSame("test", "test2");
    }

    public function testSecond(): void
    {
        $this->assertSame("test", "test");
    }

}