<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Testcontainers;
use Tests\Images\AlpineContainer;

class TestcontainersTest extends TestCase
{
    public function testWithContainerClassString()
    {
        $instance = Testcontainers::run(AlpineContainer::class);
        $running = $instance->isRunning();

        $this->assertTrue($running);
    }

    public function testWithContainerInstance()
    {
        $instance = Testcontainers::run(new AlpineContainer());
        $running = $instance->isRunning();

        $this->assertTrue($running);
    }

    public function testStopMultipleInstances()
    {
        $instance1 = Testcontainers::run(new AlpineContainer());
        $instance2 = Testcontainers::run(new AlpineContainer());
        $instance3 = Testcontainers::run(new AlpineContainer());

        $this->assertTrue($instance1->isRunning());
        $this->assertTrue($instance2->isRunning());
        $this->assertTrue($instance3->isRunning());

        Testcontainers::stop();

        $this->assertFalse($instance1->isRunning());
        $this->assertFalse($instance2->isRunning());
        $this->assertFalse($instance3->isRunning());
    }
}
