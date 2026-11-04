<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopSignalSetting;
use UnexpectedValueException;

final class StopSignalSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testStopSignalIsProvidedByStopSignalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(StopSignalSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStopSignalFromStaticStopSignal()
    {
        $container = new StopSignalSettingWithStaticContainer('alpine:latest');
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        self::assertSame('KILL', $instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testWithStopSignal()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withStopSignal('KILL')
        ;
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        self::assertSame('KILL', $instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testStopSignalIsNullByDefault()
    {
        $container = new GenericContainer('alpine:latest');
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        self::assertNull($instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testStopSignalRejectsInvalidStaticConfiguration()
    {
        $container = new StopSignalSettingWithInvalidStopSignalContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class StopSignalSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_SIGNAL = 'KILL';
}

final class StopSignalSettingWithInvalidStopSignalContainer extends GenericContainer
{
    protected static $STOP_SIGNAL = [];
}
