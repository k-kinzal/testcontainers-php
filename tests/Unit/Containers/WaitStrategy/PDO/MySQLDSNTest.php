<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;

class MySQLDSNTest extends DSNTestCase
{
    /**
     * @inheritDoc
     */
    public function resolveDSN()
    {
        return new MySQLDSN();
    }

    public function testWithDbname()
    {
        $dbname = 'test';
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withDbname($dbname);

        $this->assertTrue(strpos($dsn->toString(), $dbname) !== false);
    }

    public function testWithCharset()
    {
        $charset = 'utf8';
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withCharset($charset);

        $this->assertTrue(strpos($dsn->toString(), $charset) !== false);
    }

    public function testFullDSN()
    {
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withPort(3306)
            ->withDbname('test')
            ->withCharset('utf8');

        $this->assertEquals('mysql:host=localhost;port=3306;dbname=test;charset=utf8;', $dsn->toString());
    }
}
