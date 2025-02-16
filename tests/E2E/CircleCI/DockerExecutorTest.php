<?php

namespace Tests\E2E\CircleCI;

use PDO;
use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Testcontainers;

class DockerExecutorTest extends TestCase
{
    public function test()
    {
        if (getenv('CIRCLECI') !== 'true' || getenv('DOCKER_HOST') === false) {
            $this->markTestSkipped('This test is only for CircleCI (docker executor)');
        }

        $instance = Testcontainers::run(
            (new GenericContainer('mysql:8'))
            ->withExposedPort(3306)
            ->withEnvs([
                'MYSQL_ROOT_PASSWORD' => 'test',
            ])
            ->withWaitStrategy(
                (new PDOConnectWaitStrategy())
                    ->withDsn(new MySQLDSN())
                    ->withUsername('root')
                    ->withPassword('test')
            )
            ->withSSHPortForward('remote-docker')
        );

        $pdo = new PDO('mysql:host=127.0.0.1;port=' . $instance->getMappedPort(3306), 'root', 'test');
        $result = $pdo->query('SELECT 1')->fetchColumn();

        $this->assertSame(1, $result);
    }
}
