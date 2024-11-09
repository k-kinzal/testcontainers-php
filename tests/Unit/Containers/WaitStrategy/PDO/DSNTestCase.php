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

        $this->assertTrue(strpos($dsn->toString(), $hostname) !== false);
    }

    public function testWithPort()
    {
        $port = 3306;
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withPort($port);

        $this->assertTrue(strpos($dsn->toString(), (string) $port) !== false);
    }

    public function testPassPDO()
    {
        $dsn = $this->resolveDSN()
            ->withHost('localhost')
            ->withPort(3306);

        try {
            new PDO($dsn->toString());
        } catch (PDOException $e) {
            if ($e->getMessage() === 'could not find driver') {
                throw $e;
            }
        }

        $this->assertTrue(true);
    }
}
