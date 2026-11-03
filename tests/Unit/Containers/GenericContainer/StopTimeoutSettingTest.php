<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StopTimeoutSetting;
use UnexpectedValueException;

final class StopTimeoutSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testStopTimeoutIsProvidedByStopTimeoutSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(StopTimeoutSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStopTimeoutFromStaticStopTimeout()
    {
        $container = new StopTimeoutSettingWithStaticContainer('alpine:latest');
        $instance = $container->start();

        $this->assertSame(5, $instance->getStopTimeout());
    }

    /**
     * @return void
     */
    public function testWithStopTimeout()
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
    public function testStopTimeoutIsNullByDefault()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertNull($instance->getStopTimeout());
    }

    /**
     * @dataProvider providerWithStopTimeoutRejectsInvalidInputs
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
    public static function providerWithStopTimeoutRejectsInvalidInputs()
    {
        return [
            'case 1' => [-2],
        ];
    }

    /**
     * @return void
     */
    public function testStopTimeoutRejectsInvalidStaticConfiguration()
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
