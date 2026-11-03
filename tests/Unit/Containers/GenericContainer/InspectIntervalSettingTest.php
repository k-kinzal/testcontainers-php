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
final class InspectIntervalSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasInspectIntervalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(InspectIntervalSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticInspectInterval()
    {
        $container = new InspectIntervalSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(5, $instance->getInspectInterval());
    }

    /**
     * @return void
     */
    public function testStartWithInspectInterval()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withInspectInterval(0.5)
        ;
        $instance = $container->start();

        $this->assertSame(0.5, $instance->getInspectInterval());
    }

    /**
     * @return void
     */
    public function testDefaultInspectInterval()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(10, $instance->getInspectInterval());
    }

    /**
     * @dataProvider providerWithInspectIntervalInvalidInputs
     * @param float $seconds
     * @return void
     */
    public function testWithInspectIntervalRejectsInvalidInputs($seconds)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withInspectInterval($seconds);
    }

    /**
     * @return array<string, array{float}>
     */
    public static function providerWithInspectIntervalInvalidInputs()
    {
        return [
            'case 1' => [NAN],
        ];
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidInspectInterval()
    {
        $container = new InspectIntervalSettingWithInvalidInspectIntervalContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class InspectIntervalSettingWithStaticContainer extends GenericContainer
{
    protected static $INSPECT_INTERVAL = 5;
}

final class InspectIntervalSettingWithInvalidInspectIntervalContainer extends GenericContainer
{
    protected static $INSPECT_INTERVAL = INF;
}
