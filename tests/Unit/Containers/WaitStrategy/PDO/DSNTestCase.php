<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\PDO\DSN;

abstract class DSNTestCase extends TestCase
{
    /**
     * @return array<array{DSN}>
     */
    abstract public static function providerWithHost();

    /**
     * @return array<array{DSN}>
     */
    abstract public static function providerWithPort();

    /**
     * @return array<array{DSN}>
     */
    abstract public static function providerToStringAcceptedByPDO();

    /**
     * @dataProvider providerWithHost
     * @param DSN $dsn
     * @return void
     */
    public function testWithHost($dsn)
    {
        $hostname = 'localhost';
        $dsn = $dsn->withHost($hostname);

        self::assertTrue(strpos($dsn->toString(), $hostname) !== false);
    }

    /**
     * @dataProvider providerWithPort
     * @param DSN $dsn
     * @return void
     */
    public function testWithPort($dsn)
    {
        $port = 3306;
        $dsn = $dsn
            ->withHost('localhost')
            ->withPort($port)
        ;

        self::assertTrue(strpos($dsn->toString(), (string) $port) !== false);
    }

    /**
     * @dataProvider providerToStringAcceptedByPDO
     * @param DSN $dsn
     * @return void
     */
    public function testToStringAcceptedByPDO($dsn)
    {
        $dsn = $dsn
            ->withHost('localhost')
            ->withPort(3306)
        ;

        try {
            new \PDO($dsn->toString());
        } catch (\PDOException $e) {
            self::assertNotSame('could not find driver', $e->getMessage());
        }

        self::assertTrue(true);
    }
}
