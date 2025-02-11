<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;

class GenericContainerTest extends TestCase
{
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }
}
