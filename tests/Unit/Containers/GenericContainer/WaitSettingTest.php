<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WaitSetting;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use UnexpectedValueException;

final class WaitSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testWaitStrategyIsProvidedByWaitSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(WaitSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testWaitStrategyFromStaticWaitStrategy()
    {
        $container = new WaitSettingWithStaticWaitStrategyContainer();
        $container->start();

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testWithWaitStrategy()
    {
        $container = (new GenericContainer('nginx:latest'))
            ->withExposedPort(80)
            ->withWaitStrategy(new HostPortWaitStrategy())
        ;
        $container->start();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider providerWithWaitStrategyInvalidInputs
     * @param stdClass $waitStrategy
     * @return void
     */
    public function testWithWaitStrategyRejectsInvalidInputs($waitStrategy)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withWaitStrategy($waitStrategy);
    }

    /**
     * @return array<string, array{stdClass}>
     */
    public static function providerWithWaitStrategyInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }
}

final class WaitSettingWithStaticWaitStrategyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';

    protected static $EXPOSED_PORTS = [80];

    protected static $WAIT_STRATEGY = 'host_port';
}
