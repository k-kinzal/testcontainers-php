<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopTimeoutSetting;
use UnexpectedValueException;

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

    /**
     * @dataProvider provideWithStopTimeoutInvalidInputs
     * @param mixed $seconds
     */
    public function testWithStopTimeoutRejectsInvalidInputs($seconds)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStopTimeout($seconds);
    }

    /**
     * @return array
     */
    public static function provideWithStopTimeoutInvalidInputs()
    {
        return [
            'case 1' => [-2],
        ];
    }

    public function testStartRejectsInvalidStopTimeout()
    {
        $container = new StopTimeoutSettingWithInvalidStopTimeoutContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class StopTimeoutSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_TIMEOUT = 5;
}

class StopTimeoutSettingWithInvalidStopTimeoutContainer extends GenericContainer
{
    protected static $STOP_TIMEOUT = -2;
}
