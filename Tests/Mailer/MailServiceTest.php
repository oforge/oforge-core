<?php
declare(strict_types=1);

namespace Tests\Mailer;

use Oforge\Engine\Tests\TestCase;

final class MailServiceTest extends TestCase
{
    public function testWrongToConfiguration(): void
    {
        $this->expectExceptionMessage("Config key to not found in options");
        Oforge()->Services()->get("mail")->send([]);
        //$this->assertSame("test", "test2");
    }

    public function testWrongEmailConfiguration(): void
    {
        $this->expectExceptionMessage("tim is not a valid email.");
        Oforge()->Services()->get("mail")->send(["to" => ["tim" => "wrong"], "subject" => "test", "body"  => "testbody" ]);
        //$this->assertSame("test", "test2");
    }

    public function testWrongArgument(): void
    {
        $this->expectExceptionMessage("Expected array for to but get string");
        Oforge()->Services()->get("mail")->send(["to" => "tim", "subject" => "test", "body"  => "testbody" ]);
        //$this->assertSame("test", "test2");
    }
}