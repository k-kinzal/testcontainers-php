<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PortSetting;
use Testcontainers\Containers\PortStrategy\RandomPortStrategy;
use UnexpectedValueException;

/**
 * @internal
 *
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

    /**
     * @dataProvider provideWithPortInvalidInputs
     * @param mixed $port
     */
    public function testWithPortRejectsInvalidInputs($port)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPort($port);
    }

    /**
     * @return array
     */
    public static function provideWithPortInvalidInputs()
    {
        return [
            'case 1' => ['80abc'],
            'case 2' => ['1e3'],
            'case 3' => [true],
            'case 4' => [1.5],
            'case 5' => [0],
            'case 6' => [-1],
            'case 7' => [65536],
            'case 8' => ['999999999999999999999999999999'],
        ];
    }

    /**
     * @dataProvider provideWithPortsInvalidInputs
     * @param mixed $ports
     */
    public function testWithPortsRejectsInvalidInputs($ports)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPorts($ports);
    }

    /**
     * @return array
     */
    public static function provideWithPortsInvalidInputs()
    {
        return [
            'case 1' => [[80, 65536]],
        ];
    }

    /**
     * @dataProvider provideWithPortStrategyInvalidInputs
     * @param mixed $strategy
     */
    public function testWithPortStrategyRejectsInvalidInputs($strategy)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPortStrategy($strategy);
    }

    /**
     * @return array
     */
    public static function provideWithPortStrategyInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }

    public function testStartRejectsInvalidExposedPorts()
    {
        $container = new PortSettingWithInvalidExposedPortsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    public function testStartRejectsInvalidPorts()
    {
        $container = new PortSettingWithInvalidPortsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    public function testStartRejectsInvalidPortStrategy()
    {
        $container = new PortSettingWithInvalidPortStrategyContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
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

class PortSettingWithInvalidExposedPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 'garbage'];
}

class PortSettingWithInvalidPortsContainer extends GenericContainer
{
    protected static $PORTS = false;
}

class PortSettingWithInvalidPortStrategyContainer extends GenericContainer
{
    protected static $PORT_STRATEGY = [];
}
