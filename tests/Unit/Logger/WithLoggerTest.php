<?php

namespace Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use UnexpectedValueException;

final class WithLoggerTest extends TestCase
{
    /**
     * @dataProvider providerWithLoggerRejectsInvalidInputs
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
    public static function providerWithLoggerRejectsInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }
}
