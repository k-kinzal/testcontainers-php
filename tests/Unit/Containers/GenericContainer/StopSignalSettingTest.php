<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopSignalSetting;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class StopSignalSettingTest extends TestCase
{
    public function testHasStopSignalSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StopSignalSetting::class, $uses);
    }

    public function testStaticStopSignal()
    {
        $container = new StopSignalSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame('KILL', $instance->getStopSignal());
    }

    public function testStartWithStopSignal()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withStopSignal('KILL')
        ;
        $instance = $container->start();

        $this->assertSame('KILL', $instance->getStopSignal());
    }

    public function testDefaultStopSignalIsNull()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertNull($instance->getStopSignal());
    }

    public function testStartRejectsInvalidStopSignal()
    {
        $container = new StopSignalSettingWithInvalidStopSignalContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class StopSignalSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_SIGNAL = 'KILL';
}

class StopSignalSettingWithInvalidStopSignalContainer extends GenericContainer
{
    protected static $STOP_SIGNAL = [];
}
