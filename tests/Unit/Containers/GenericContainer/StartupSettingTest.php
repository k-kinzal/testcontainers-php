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
    /**
     * @return void
     */
    public function testHasStartupSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StartupSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupTimeoutContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    /**
     * @return void
     */
    public function testStaticStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupCheckStrategyContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    /**
     * @return void
     */
    public function testStartWithStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupTimeout(1)
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    /**
     * @return void
     */
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
     * @dataProvider providerWithStartupTimeoutInvalidInputs
     * @param int $timeout
     * @return void
     */
    public function testWithStartupTimeoutRejectsInvalidInputs($timeout)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStartupTimeout($timeout);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function providerWithStartupTimeoutInvalidInputs()
    {
        return [
            'case 1' => [-1],
        ];
    }

    /**
     * @dataProvider providerWithStartupConflictRetriesInvalidInputs
     * @param int $retryAttempts
     * @return void
     */
    public function testWithStartupConflictRetriesRejectsInvalidInputs($retryAttempts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withStartupConflictRetries($retryAttempts);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function providerWithStartupConflictRetriesInvalidInputs()
    {
        return [
            'case 1' => [0],
        ];
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidStartupTimeout()
    {
        $container = new StartupSettingWithInvalidStartupTimeoutContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    /**
     * @return void
     */
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
