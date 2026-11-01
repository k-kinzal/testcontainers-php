<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\WaitSetting;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use UnexpectedValueException;

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

    /**
     * @dataProvider provideWithWaitStrategyInvalidInputs
     * @param mixed $waitStrategy
     */
    public function testWithWaitStrategyRejectsInvalidInputs($waitStrategy)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withWaitStrategy($waitStrategy);
    }

    /**
     * @return array
     */
    public static function provideWithWaitStrategyInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }
}

class WaitSettingWithStaticWaitStrategyContainer extends GenericContainer
{
    protected static $IMAGE = 'nginx:latest';

    protected static $EXPOSED_PORTS = [80];

    protected static $WAIT_STRATEGY = 'host_port';
}
