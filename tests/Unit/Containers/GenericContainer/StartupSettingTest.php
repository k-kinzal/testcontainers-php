<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StartupSetting;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckFailedException;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class StartupSettingTest extends TestCase
{
    public function testHasStartupSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StartupSetting::class, $uses);
    }

    public function testStaticStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupTimeoutContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStaticStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupCheckStrategyContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStartWithStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupTimeout(1)
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStartWithStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    /**
     * @dataProvider provideWithStartupTimeoutInvalidInputs
     * @param mixed $timeout
     */
    public function testWithStartupTimeoutRejectsInvalidInputs($timeout)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStartupTimeout($timeout);
    }

    /**
     * @return array
     */
    public static function provideWithStartupTimeoutInvalidInputs()
    {
        return [
            'case 1' => [-1],
        ];
    }

    /**
     * @dataProvider provideWithStartupConflictRetriesInvalidInputs
     * @param mixed $retryAttempts
     */
    public function testWithStartupConflictRetriesRejectsInvalidInputs($retryAttempts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStartupConflictRetries($retryAttempts);
    }

    /**
     * @return array
     */
    public static function provideWithStartupConflictRetriesInvalidInputs()
    {
        return [
            'case 1' => [0],
        ];
    }

    public function testStartRejectsInvalidStartupTimeout()
    {
        $container = new StartupSettingWithInvalidStartupTimeoutContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    public function testStartRejectsInvalidStartupConflictRetryAttempts()
    {
        $container = new StartupSettingWithInvalidStartupConflictRetryAttemptsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class StartupSettingWithStaticStartupTimeoutContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = 1;
}

class StartupSettingWithStaticStartupCheckStrategyContainer extends GenericContainer
{
    protected static $STARTUP_CHECK_STRATEGY = 'one_shot';
}

class StartupSettingWithInvalidStartupTimeoutContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = -1;
}

class StartupSettingWithInvalidStartupConflictRetryAttemptsContainer extends GenericContainer
{
    protected static $STARTUP_CONFLICT_RETRY_ATTEMPTS = 0;
}
