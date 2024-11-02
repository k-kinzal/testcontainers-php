<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Hook\AfterStartHook;
use Testcontainers\Testcontainers;
use Tests\Images\AlpineContainer;

class TestcontainersTest extends TestCase
{
    public function testWithContainerClassString()
    {
        try {
            $instance = Testcontainers::run(AlpineContainer::class);
            $running = $instance->isRunning();

            $this->assertTrue($running);
        } finally {
            Testcontainers::stop();
        }
    }

    public function testWithContainerInstance()
    {
        try {
            $instance = Testcontainers::run(new AlpineContainer());
            $running = $instance->isRunning();

            $this->assertTrue($running);
        } finally {
            Testcontainers::stop();
        }
    }
}
