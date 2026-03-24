<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopTimeoutSetting;

/**
 * @internal
 *
 * @coversNothing
 */
class StopTimeoutSettingTest extends TestCase
{
    public function testHasStopTimeoutSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StopTimeoutSetting::class, $uses);
    }

    public function testStaticStopTimeout()
    {
        $container = new StopTimeoutSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(5, $instance->getStopTimeout());
    }

    public function testStartWithStopTimeout()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withStopTimeout(0)
        ;
        $instance = $container->start();

        $this->assertSame(0, $instance->getStopTimeout());
    }

    public function testDefaultStopTimeoutIsNull()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertNull($instance->getStopTimeout());
    }
}

class StopTimeoutSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_TIMEOUT = 5;
}
