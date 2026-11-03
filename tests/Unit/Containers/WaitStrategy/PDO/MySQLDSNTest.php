<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;

/**
 * @internal
 *
 * @coversNothing
 */
class MySQLDSNTest extends DSNTestCase
{
    public function resolveDSN()
    {
        return new MySQLDSN();
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

        $this->assertTrue(strpos($dsn->toString(), $dbname) !== false);
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

        $this->assertTrue(strpos($dsn->toString(), $charset) !== false);
    }

    /**
     * @return void
     */
    public function testFullDSN()
    {
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withPort(3306)
            ->withDbname('test')
            ->withCharset('utf8')
        ;

        $this->assertEquals('mysql:host=localhost;port=3306;dbname=test;charset=utf8;', $dsn->toString());
    }
}
