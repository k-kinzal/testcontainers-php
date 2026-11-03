<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StartupSetting;
use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckFailedException;
use UnexpectedValueException;

final class StartupSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testStartupTimeoutIsProvidedByStartupSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(StartupSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStartupTimeoutFromStaticStartupTimeout()
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
    public function testStartupCheckStrategyFromStaticStartupCheckStrategy()
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
    public function testWithStartupTimeout()
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
    public function testWithStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupCheckStrategy(new OneShotStartupCheckStrategy())
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    /**
     * @dataProvider providerWithStartupTimeoutRejectsInvalidInputs
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
    public static function providerWithStartupTimeoutRejectsInvalidInputs()
    {
        return [
            'case 1' => [-1],
        ];
    }

    /**
     * @dataProvider providerWithStartupConflictRetriesRejectsInvalidInputs
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
    public static function providerWithStartupConflictRetriesRejectsInvalidInputs()
    {
        return [
            'case 1' => [0],
        ];
    }

    /**
     * @return void
     */
    public function testStartupTimeoutRejectsInvalidStaticConfiguration()
    {
        $container = new StartupSettingWithInvalidStartupTimeoutContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }

    /**
     * @return void
     */
    public function testStartupConflictRetryAttemptsRejectsInvalidStaticConfiguration()
    {
        $container = new StartupSettingWithInvalidStartupConflictRetryAttemptsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class StartupSettingWithStaticStartupTimeoutContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = 1;
}

final class StartupSettingWithStaticStartupCheckStrategyContainer extends GenericContainer
{
    protected static $STARTUP_CHECK_STRATEGY = 'one_shot';
}

final class StartupSettingWithInvalidStartupTimeoutContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = -1;
}

final class StartupSettingWithInvalidStartupConflictRetryAttemptsContainer extends GenericContainer
{
    protected static $STARTUP_CONFLICT_RETRY_ATTEMPTS = 0;
}
