<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\InspectIntervalSetting;
use UnexpectedValueException;

final class InspectIntervalSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testInspectIntervalIsProvidedByInspectIntervalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(InspectIntervalSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testInspectIntervalFromStaticInspectInterval()
    {
        $container = new InspectIntervalSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        self::assertSame(5, $instance->getInspectInterval());
    }

    /**
     * @return void
     */
    public function testWithInspectInterval()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withInspectInterval(0.5)
        ;
        $instance = $container->start();

        self::assertSame(0.5, $instance->getInspectInterval());
    }

    /**
     * @return void
     */
    public function testInspectIntervalByDefault()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        self::assertSame(10, $instance->getInspectInterval());
    }

    /**
     * @dataProvider providerWithInspectIntervalRejectsInvalidInputs
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
    public static function providerWithInspectIntervalRejectsInvalidInputs()
    {
        return [
            'case 1' => [NAN],
        ];
    }

    /**
     * @return void
     */
    public function testInspectIntervalRejectsInvalidStaticConfiguration()
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
