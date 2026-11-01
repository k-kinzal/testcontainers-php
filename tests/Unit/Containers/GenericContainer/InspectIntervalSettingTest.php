<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\InspectIntervalSetting;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class InspectIntervalSettingTest extends TestCase
{
    public function testHasInspectIntervalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(InspectIntervalSetting::class, $uses);
    }

    public function testStaticInspectInterval()
    {
        $container = new InspectIntervalSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(5, $instance->getInspectInterval());
    }

    public function testStartWithInspectInterval()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withInspectInterval(0.5)
        ;
        $instance = $container->start();

        $this->assertSame(0.5, $instance->getInspectInterval());
    }

    public function testDefaultInspectInterval()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(10, $instance->getInspectInterval());
    }

    /**
     * @dataProvider provideWithInspectIntervalInvalidInputs
     * @param mixed $seconds
     */
    public function testWithInspectIntervalRejectsInvalidInputs($seconds)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withInspectInterval($seconds);
    }

    /**
     * @return array
     */
    public static function provideWithInspectIntervalInvalidInputs()
    {
        return [
            'case 1' => [NAN],
        ];
    }

    public function testStartRejectsInvalidInspectInterval()
    {
        $container = new InspectIntervalSettingWithInvalidInspectIntervalContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class InspectIntervalSettingWithStaticContainer extends GenericContainer
{
    protected static $INSPECT_INTERVAL = 5;
}

class InspectIntervalSettingWithInvalidInspectIntervalContainer extends GenericContainer
{
    protected static $INSPECT_INTERVAL = INF;
}
