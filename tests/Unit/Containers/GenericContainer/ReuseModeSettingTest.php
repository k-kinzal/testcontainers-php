<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\ReuseModeSetting;
use Testcontainers\Containers\ReuseMode;
use UnexpectedValueException;

final class ReuseModeSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasReuseModeSetting()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(ReuseModeSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testDefaultReuseModeIsAdd()
    {
        $container = new GenericContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isAdd());
    }

    /**
     * @return void
     */
    public function testWithReuseModeRestart()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withReuseMode(ReuseMode::RESTART());

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isRestart());
    }

    /**
     * @return void
     */
    public function testWithReuseModeReuse()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withReuseMode(ReuseMode::REUSE());

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isReuse());
    }

    /**
     * @return void
     */
    public function testStaticReuseMode()
    {
        $container = new ReuseModeSettingWithStaticReuseModeContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isReuse());
    }

    /**
     * @return void
     */
    public function testStaticReuseModeRestart()
    {
        $container = new ReuseModeSettingWithStaticRestartModeContainer('alpine:latest');

        $reuseMode = $container->reuseMode();

        $this->assertTrue($reuseMode->isRestart());
    }

    /**
     * @return void
     */
    public function testReuseModeRejectsInvalidDefault()
    {
        $container = new ReuseModeSettingWithInvalidReuseModeContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->reuseMode();
    }
}

final class ReuseModeSettingWithStaticReuseModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = 'reuse';
}

final class ReuseModeSettingWithStaticRestartModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = 'restart';
}

final class ReuseModeSettingWithInvalidReuseModeContainer extends GenericContainer
{
    protected static $REUSE_MODE = false;
}
