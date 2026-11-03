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
    public function testHasStopSignalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(StopSignalSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticStopSignal()
    {
        $container = new StopSignalSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame('KILL', $instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testStartWithStopSignal()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withStopSignal('KILL')
        ;
        $instance = $container->start();

        $this->assertSame('KILL', $instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testDefaultStopSignalIsNull()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertNull($instance->getStopSignal());
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidStopSignal()
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
