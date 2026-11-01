<?php

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use UnexpectedValueException;

/**
 * @internal
 * @coversNothing
 */
class AutoRemoveOnExitSettingTest extends TestCase
{
    /**
     * @dataProvider provideWithAutoRemoveOnExitInvalidInputs
     * @param mixed $autoRemoveOnExit
     */
    public function testWithAutoRemoveOnExitRejectsInvalidInputs($autoRemoveOnExit)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withAutoRemoveOnExit($autoRemoveOnExit);
    }

    /**
     * @return array
     */
    public static function provideWithAutoRemoveOnExitInvalidInputs()
    {
        return [
            'case 1' => [1],
        ];
    }

    public function testStartRejectsInvalidAutoRemoveOnExit()
    {
        $container = new AutoRemoveOnExitSettingWithInvalidAutoRemoveOnExitContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class AutoRemoveOnExitSettingWithInvalidAutoRemoveOnExitContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = 1;
}
