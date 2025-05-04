<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PortSetting;
use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

/**
 * @internal
 * @coversNothing
 */
class PortSettingTest extends TestCase
{
    public function testHasPortSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(PortSetting::class, $uses);
    }

    public function testStaticExposedPorts()
    {
        $container = (new PortSettingWithExposedPortsContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStaticExpose()
    {
        $container = (new PortSettingWithExposeContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStaticPorts()
    {
        $container = (new PortSettingWithPortsContainer('alpine:latest'));
        $instance = $container->start();

        $this->assertSame([80, 443], $instance->getExposedPorts());
    }

    public function testStartWithExposedPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExpose()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExpose(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPort(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithExposes()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposes([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }

    public function testStartWithPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPorts([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        $this->assertSame([80], $instance->getExposedPorts());
    }
}

class PortSettingWithExposedPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 443];
}

class PortSettingWithExposeContainer extends GenericContainer
{
    protected static $EXPOSE = [80, 443];
}

class PortSettingWithPortsContainer extends GenericContainer
{
    protected static $PORTS = [80, 443];
}
