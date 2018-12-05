<?php
declare(strict_types=1);

namespace Tests\ConfigService;

use Oforge\Engine\Tests\TestCase;

final class ConfigTest extends TestCase
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