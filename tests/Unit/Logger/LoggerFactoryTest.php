<?php

namespace Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Testcontainers\Logger\ConsoleLogger;
use Testcontainers\Logger\LoggerFactory;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoggerFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateWithoutEnvironmentVariables()
    {
        putenv('TESTCONTAINERS_LOGGER');
        putenv('TESTCONTAINERS_LOG_LEVEL');
        putenv('RUNNER_DEBUG');
        putenv('ACTIONS_STEP_DEBUG');
        putenv('CI_DEBUG_TRACE');
        putenv('SYSTEM_DEBUG');

        $this->assertInstanceOf(NullLogger::class, LoggerFactory::create());
    }

    /**
     * @return void
     */
    public function testCreateFromLogger()
    {
        try {
            putenv('TESTCONTAINERS_LOGGER=Testcontainers\Logger\ConsoleLogger');

            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());
        } finally {
            putenv('TESTCONTAINERS_LOGGER');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromLoggerWithLeadingBackslash()
    {
        try {
            putenv('TESTCONTAINERS_LOGGER=\Testcontainers\Logger\ConsoleLogger');

            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());
        } finally {
            putenv('TESTCONTAINERS_LOGGER');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromLoggerNotImplementingLoggerInterface()
    {
        try {
            putenv('TESTCONTAINERS_LOGGER=stdClass');

            LoggerFactory::create();
            $this->fail('Expected UnexpectedValueException');
        } catch (UnexpectedValueException $e) {
            $this->assertTrue(strpos($e->getMessage(), 'stdClass') !== false);
        } finally {
            putenv('TESTCONTAINERS_LOGGER');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromUndefinedLogger()
    {
        try {
            putenv('TESTCONTAINERS_LOGGER=Undefined\Logger');

            LoggerFactory::create();
            $this->fail('Expected UnexpectedValueException');
        } catch (UnexpectedValueException $e) {
            $this->assertTrue(strpos($e->getMessage(), 'Undefined\Logger') !== false);
        } finally {
            putenv('TESTCONTAINERS_LOGGER');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromGitHubActionsRunnerDebug()
    {
        try {
            putenv('RUNNER_DEBUG=1');
            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());

            putenv('RUNNER_DEBUG=0');
            $this->assertInstanceOf(NullLogger::class, LoggerFactory::create());
        } finally {
            putenv('RUNNER_DEBUG');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromGitHubActionsStepDebug()
    {
        try {
            putenv('ACTIONS_STEP_DEBUG=true');

            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());
        } finally {
            putenv('ACTIONS_STEP_DEBUG');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromGitLabDebugTrace()
    {
        try {
            putenv('CI_DEBUG_TRACE=true');

            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());
        } finally {
            putenv('CI_DEBUG_TRACE');
        }
    }

    /**
     * @return void
     */
    public function testCreateFromAzurePipelinesSystemDebug()
    {
        try {
            putenv('SYSTEM_DEBUG=True');

            $this->assertInstanceOf(ConsoleLogger::class, LoggerFactory::create());
        } finally {
            putenv('SYSTEM_DEBUG');
        }
    }
}
