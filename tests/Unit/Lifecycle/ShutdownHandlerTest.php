<?php

namespace Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use Testcontainers\Lifecycle\ShutdownHandler;

/**
 * @internal
 *
 * @coversNothing
 */
class ShutdownHandlerTest extends TestCase
{
    public function testRegisterSetsRegisteredFlag()
    {
        $this->resetShutdownHandler();

        $ref = new \ReflectionClass(ShutdownHandler::class);
        $registered = $ref->getProperty('registered');

        $this->assertFalse($registered->getValue());

        ShutdownHandler::register(function () {});

        $this->assertTrue($registered->getValue());

        $this->resetShutdownHandler();
    }

    public function testRegisterDoesNotDoubleRegister()
    {
        $this->resetShutdownHandler();

        $callCount = 0;

        ShutdownHandler::register(function () use (&$callCount) {
            ++$callCount;
        });

        // Second call should be a no-op
        ShutdownHandler::register(function () use (&$callCount) {
            ++$callCount;
        });

        $ref = new \ReflectionClass(ShutdownHandler::class);
        $registered = $ref->getProperty('registered');

        $this->assertTrue($registered->getValue());

        $this->resetShutdownHandler();
    }

    public function testCalledFlagDefaultsFalse()
    {
        $this->resetShutdownHandler();

        $ref = new \ReflectionClass(ShutdownHandler::class);
        $called = $ref->getProperty('called');

        $this->assertFalse($called->getValue());

        $this->resetShutdownHandler();
    }

    private function resetShutdownHandler()
    {
        $ref = new \ReflectionClass(ShutdownHandler::class);

        $registered = $ref->getProperty('registered');
        $registered->setValue(null, false);

        $called = $ref->getProperty('called');
        $called->setValue(null, false);
    }
}
