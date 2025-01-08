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

    public function testStaticExposedPorts()
    {
        $container = (new ExposedPortSettingWithExposedPortsContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStaticExpose()
    {
        $container = (new ExposedPortSettingWithExposeContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStaticPorts()
    {
        $container = (new ExposedPortSettingWithPortsContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStartWithExposedPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExpose()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExpose(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPort(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts([80])
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExposes()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposes([80])
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPorts([80])
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }
}

class ExposedPortSettingWithExposedPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 443];

    // TODO: implements default port strategy
    protected static $PORT_STRATEGY = 'local_random';
}

class ExposedPortSettingWithExposeContainer extends GenericContainer
{
    protected static $EXPOSE = [80, 443];

    protected static $PORT_STRATEGY = 'local_random';
}

class ExposedPortSettingWithPortsContainer extends GenericContainer
{
    protected static $PORTS = [80, 443];

    protected static $PORT_STRATEGY = 'local_random';
}
