<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;

class MySQLDSNTest extends DSNTestCase
{
    public function resolveDSN()
    {
        return new MySQLDSN();
    }

    public function testWithDbname()
    {
        $dbname = 'test';
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withDbname($dbname)
        ;

        $this->assertTrue(false !== strpos($dsn->toString(), $dbname));
    }

    public function testWithCharset()
    {
        $charset = 'utf8';
        $dsn = (new MySQLDSN())
            ->withHost('localhost')
            ->withCharset($charset)
        ;

        $this->assertTrue(false !== strpos($dsn->toString(), $charset));
    }

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
