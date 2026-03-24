<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopSignalSetting;

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
}

class StopSignalSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_SIGNAL = 'KILL';
}
