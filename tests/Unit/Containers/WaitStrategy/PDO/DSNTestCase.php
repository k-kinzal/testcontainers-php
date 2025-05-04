<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\PDO\DSN;

abstract class DSNTestCase extends TestCase
{
    /**
     * @return DSN
     */
    abstract public function resolveDSN();

    public function testWithHost()
    {
        $hostname = 'localhost';
        $dsn = $this->resolveDSN()->withHost($hostname);

        $this->assertTrue(false !== strpos($dsn->toString(), $hostname));
    }

    public function testWithPort()
    {
        $port = 3306;
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withPort($port)
        ;

        $this->assertTrue(false !== strpos($dsn->toString(), (string) $port));
    }

    public function testPassPDO()
    {
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withPort(3306)
        ;

        try {
            new PDO($dsn->toString());
        } catch (PDOException $e) {
            if ('could not find driver' === $e->getMessage()) {
                throw $e;
            }
        }

        $this->assertTrue(true);
    }
}
