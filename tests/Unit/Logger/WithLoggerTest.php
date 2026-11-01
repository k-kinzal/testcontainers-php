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
class WithLoggerTest extends TestCase
{
    /**
     * @dataProvider provideWithLoggerInvalidInputs
     * @param mixed $logger
     */
    public function testWithLoggerRejectsInvalidInputs($logger)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withLogger($logger);
    }

    /**
     * @return array
     */
    public static function provideWithLoggerInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }
}
