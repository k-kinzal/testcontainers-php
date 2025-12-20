<?php

namespace Tests\E2E\CircleCI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Testcontainers;

/**
 * @internal
 *
 * @coversNothing
 */
class DockerExecutorTest extends TestCase
{
    public function test()
    {
        if (getenv('CIRCLECI') !== 'true' || getenv('DOCKER_HOST') === false) {
            $this->markTestSkipped('This test is only for CircleCI (docker executor)');
        }

        $output = new StreamOutput(STDERR, OutputInterface::VERBOSITY_DEBUG);
        $logger = new ConsoleLogger($output);

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
                ->withLogger($logger)
        );

        $pdo = new \PDO('mysql:host=127.0.0.1;port='.$instance->getMappedPort(3306), 'root', 'test');
        $result = $pdo->query('SELECT 1')->fetchColumn();

        $this->assertSame(1, $result);
    }
}
