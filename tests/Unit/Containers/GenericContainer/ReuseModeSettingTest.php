<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\ReuseModeSetting;
use Testcontainers\Containers\ReuseMode;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class ReuseModeSettingTest extends TestCase
{
    public function testHasReuseModeSetting()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(ReuseModeSetting::class, $uses);
    }

    public function testDefaultReuseModeIsAdd()
    {
        $container = new GenericContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isAdd());
    }

    public function testWithReuseModeRestart()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withReuseMode(ReuseMode::RESTART());

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isRestart());
    }

    public function testWithReuseModeReuse()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withReuseMode(ReuseMode::REUSE());

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isReuse());
    }

    public function testStaticReuseMode()
    {
        $container = new ReuseModeSettingWithStaticReuseModeContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isReuse());
    }

    public function testStaticReuseModeRestart()
    {
        $container = new ReuseModeSettingWithStaticRestartModeContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isRestart());
    }

    public function testReuseModeRejectsInvalidDefault()
    {
        $container = new ReuseModeSettingWithInvalidReuseModeContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->reuseMode();
    }
}

class ReuseModeSettingWithStaticReuseModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = 'reuse';
}

class ReuseModeSettingWithStaticRestartModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = 'restart';
}

class ReuseModeSettingWithInvalidReuseModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = false;
}
