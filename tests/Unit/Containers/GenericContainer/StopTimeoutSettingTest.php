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
final class StopTimeoutSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasStopTimeoutSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StopTimeoutSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticStopTimeout()
    {
        $container = new StopTimeoutSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(5, $instance->getStopTimeout());
    }

    /**
     * @return void
     */
    public function testStartWithStopTimeout()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withStopTimeout(0)
        ;
        $instance = $container->start();

        $this->assertSame(0, $instance->getStopTimeout());
    }

    /**
     * @return void
     */
    public function testDefaultStopTimeoutIsNull()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertNull($instance->getStopTimeout());
    }

    /**
     * @dataProvider providerWithStopTimeoutInvalidInputs
     * @param int $seconds
     * @return void
     */
    public function testWithStopTimeoutRejectsInvalidInputs($seconds)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStopTimeout($seconds);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function providerWithStopTimeoutInvalidInputs()
    {
        return [
            'case 1' => [-2],
        ];
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidStopTimeout()
    {
        $container = new StopTimeoutSettingWithInvalidStopTimeoutContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class StopTimeoutSettingWithStaticContainer extends GenericContainer
{
    protected static $STOP_TIMEOUT = 5;
}

final class StopTimeoutSettingWithInvalidStopTimeoutContainer extends GenericContainer
{
    protected static $STOP_TIMEOUT = -2;
}
