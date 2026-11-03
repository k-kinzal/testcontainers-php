<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;

final class MySQLDSNTest extends DSNTestCase
{
    /**
     * @return array<array{MySQLDSN}>
     */
    public static function providerWithHost()
    {
        return [[new MySQLDSN()]];
    }

    /**
     * @return array<array{MySQLDSN}>
     */
    public static function providerWithPort()
    {
        return [[new MySQLDSN()]];
    }

    /**
     * @return array<array{MySQLDSN}>
     */
    public static function providerToStringAcceptedByPDO()
    {
        return [[new MySQLDSN()]];
    }

    /**
     * @return void
     */
    public function testWithDbname()
    {
        $dbname = 'test';
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withDbname($dbname)
        ;

        self::assertTrue(strpos($dsn->toString(), $dbname) !== false);
    }

    /**
     * @return void
     */
    public function testWithCharset()
    {
        $charset = 'utf8';
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withCharset($charset)
        ;

        self::assertTrue(strpos($dsn->toString(), $charset) !== false);
    }

    /**
     * @return void
     */
    public function testToStringWithAllComponents()
    {
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withPort(3306)
            ->withDbname('test')
            ->withCharset('utf8')
        ;

        self::assertEquals('mysql:host=localhost;port=3306;dbname=test;charset=utf8;', $dsn->toString());
    }
}
