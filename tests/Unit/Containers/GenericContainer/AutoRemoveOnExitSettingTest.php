<?php

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use UnexpectedValueException;

final class AutoRemoveOnExitSettingTest extends TestCase
{
    /**
     * @dataProvider providerWithAutoRemoveOnExitInvalidInputs
     * @param int $autoRemoveOnExit
     * @return void
     */
    public function testWithAutoRemoveOnExitRejectsInvalidInputs($autoRemoveOnExit)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withAutoRemoveOnExit($autoRemoveOnExit);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function providerWithAutoRemoveOnExitInvalidInputs()
    {
        return [
            'case 1' => [1],
        ];
    }

    /**
     * @return void
     */
    public function testAutoRemoveOnExitRejectsInvalidStaticConfiguration()
    {
        $container = new AutoRemoveOnExitSettingWithInvalidAutoRemoveOnExitContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class AutoRemoveOnExitSettingWithInvalidAutoRemoveOnExitContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = 1;
}
