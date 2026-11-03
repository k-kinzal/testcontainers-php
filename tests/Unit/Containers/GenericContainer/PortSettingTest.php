<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PortSetting;
use Testcontainers\Containers\PortStrategy\RandomPortStrategy;
use UnexpectedValueException;

final class PortSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testPortsIsProvidedByPortSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(PortSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testExposedPortsFromStaticExposedPorts()
    {
        $container = (new PortSettingWithExposedPortsContainer('alpine:latest'));
        $instance = $container->start();

        self::assertSame([80, 443], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testExposedPortsFromStaticExpose()
    {
        $container = (new PortSettingWithExposeContainer('alpine:latest'));
        $instance = $container->start();

        self::assertSame([80, 443], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testExposedPortsFromStaticPorts()
    {
        $container = (new PortSettingWithPortsContainer('alpine:latest'));
        $instance = $container->start();

        self::assertSame([80, 443], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithExposedPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithExpose()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExpose(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithPort()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPort(80)
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithExposes()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposes([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @return void
     */
    public function testWithPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPorts([80])
            ->withPortStrategy(new RandomPortStrategy())
        ;
        $instance = $container->start();

        self::assertSame([80], $instance->getExposedPorts());
    }

    /**
     * @dataProvider providerWithPortRejectsInvalidInputs
     * @param bool|float|int|string $port
     * @return void
     */
    public function testWithPortRejectsInvalidInputs($port)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPort($port);
    }

    /**
     * @return array<string, array{bool|float|int|string}>
     */
    public static function providerWithPortRejectsInvalidInputs()
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
     * @dataProvider providerWithPortsRejectsInvalidInputs
     * @param array{int, int} $ports
     * @return void
     */
    public function testWithPortsRejectsInvalidInputs($ports)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPorts($ports);
    }

    /**
     * @return array<string, array{array{int, int}}>
     */
    public static function providerWithPortsRejectsInvalidInputs()
    {
        return [
            'case 1' => [[80, 65536]],
        ];
    }

    /**
     * @dataProvider providerWithPortStrategyRejectsInvalidInputs
     * @param stdClass $strategy
     * @return void
     */
    public function testWithPortStrategyRejectsInvalidInputs($strategy)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withPortStrategy($strategy);
    }

    /**
     * @return array<string, array{stdClass}>
     */
    public static function providerWithPortStrategyRejectsInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }

    /**
     * @return void
     */
    public function testExposedPortsRejectsInvalidStaticConfiguration()
    {
        $container = new PortSettingWithInvalidExposedPortsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    /**
     * @return void
     */
    public function testPortsRejectsInvalidStaticConfiguration()
    {
        $container = new PortSettingWithInvalidPortsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    /**
     * @return void
     */
    public function testPortStrategyRejectsInvalidStaticConfiguration()
    {
        $container = new PortSettingWithInvalidPortStrategyContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class PortSettingWithExposedPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 443];
}

final class PortSettingWithExposeContainer extends GenericContainer
{
    protected static $EXPOSE = [80, 443];
}

final class PortSettingWithPortsContainer extends GenericContainer
{
    protected static $PORTS = [80, 443];
}

final class PortSettingWithInvalidExposedPortsContainer extends GenericContainer
{
    protected static $EXPOSED_PORTS = [80, 'garbage'];
}

final class PortSettingWithInvalidPortsContainer extends GenericContainer
{
    protected static $PORTS = false;
}

final class PortSettingWithInvalidPortStrategyContainer extends GenericContainer
{
    protected static $PORT_STRATEGY = [];
}
