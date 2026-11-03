<?php

namespace Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use UnexpectedValueException;

/**
 * @internal
 * @coversNothing
 */
final class WithLoggerTest extends TestCase
{
    /**
     * @dataProvider providerWithLoggerInvalidInputs
     * @param stdClass $logger
     * @return void
     */
    public function testWithLoggerRejectsInvalidInputs($logger)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withLogger($logger);
    }

    /**
     * @return array<string, array{stdClass}>
     */
    public static function providerWithLoggerInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }
}
