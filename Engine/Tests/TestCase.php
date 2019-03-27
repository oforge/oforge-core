<?php
namespace Oforge\Engine\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        $this->engine = Oforge();
    }

    protected function tearDown() : void
    {

    }


}