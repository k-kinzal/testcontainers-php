<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\ExposedPortSetting;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;

class ExposedPortSettingTest extends TestCase
{
    public function testHasMountSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(ExposedPortSetting::class, $uses);
    }

    public function testStaticPorts()
    {
        $container = (new ExposedPortSettingWithPortsContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }
}

class ExposedPortSettingWithPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 443];

    protected static $PORT_STRATEGY = 'local_random';
}
