<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WaitSetting;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
class WaitSettingTest extends TestCase
{
    public function testHasWaitSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(WaitSetting::class, $uses);
    }

    public function testStaticWaitStrategy()
    {
        $container = new WaitSettingWithStaticWaitStrategyContainer();
        $container->start();

        $this->assertTrue(true);
    }

    public function testStartWithWaitStrategy()
    {
        $container = (new GenericContainer('nginx:latest'))
            ->withExposedPort(80)
            ->withWaitStrategy(new HostPortWaitStrategy())
        ;
        $container->start();

        $this->assertTrue(true);
    }
}

class WaitSettingWithStaticWaitStrategyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';

    protected static $EXPOSED_PORTS = [80];

    protected static $WAIT_STRATEGY = 'host_port';
}
