<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Containers\Types\Mount;
use Testcontainers\Containers\Types\VolumeFrom;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use Testcontainers\Containers\PortStrategy\StaticPortStrategy;
use Testcontainers\Exceptions\InvalidFormatException;

class InputValidationTest extends TestCase
{
    public function testFluentSettingsRejectInvalidInputsBeforeDockerIsUsed()
    {
        $container = new ValidationContainer('alpine');
        $cases = [
            ['withEnv', ['KEY', false]],
            ['withEnvs', [['KEY' => new \stdClass()]]],
            ['withLabel', [[], 'value']],
            ['withLabels', ['not an array']],
            ['withPrivilegedMode', ['false']],
            ['withAutoRemoveOnExit', [1]],
            ['withPort', ['80abc']],
            ['withPort', ['1e3']],
            ['withPort', [true]],
            ['withPort', [1.5]],
            ['withPort', [0]],
            ['withPort', [-1]],
            ['withPort', [65536]],
            ['withPort', ['999999999999999999999999999999']],
            ['withPorts', [[80, 65536]]],
            ['withCommands', [['echo', new \stdClass()]]],
            ['withNetworkAliases', [['ok', []]]],
            ['withWorkingDirectory', [[]]],
            ['withInspectInterval', [NAN]],
            ['withStartupTimeout', [-1]],
            ['withStartupConflictRetries', [0]],
            ['withStopTimeout', [-2]],
            ['withWaitStrategy', [new \stdClass()]],
            ['withPortStrategy', [new \stdClass()]],
            ['withDockerClient', [new \stdClass()]],
            ['withLogger', [new \stdClass()]],
            ['withExtraHosts', [false]],
            ['withMounts', [false]],
            ['withSSHPortForward', ['host', 'bad']],
        ];
        foreach ($cases as $case) {
            $this->assertInvalid(function () use ($container, $case) {
                call_user_func_array([$container, $case[0]], $case[1]);
            });
        }
    }

    public function testStaticDefaultsAreValidatedOnEveryRead()
    {
        $container = new ValidationContainer('alpine');
        $cases = [
            ['ENV', ['KEY' => false], 'env'],
            ['LABELS', [1 => 'value'], 'labels'],
            ['EXPOSED_PORTS', [80, 'garbage'], 'exposedPorts'],
            ['PORTS', false, 'exposedPorts'],
            ['PRIVILEGED', 'false', 'privileged'],
            ['AUTO_REMOVE_ON_EXIT', 1, 'autoRemoveOnExit'],
            ['NAME', [], 'name'],
            ['COMMANDS', ['echo', false], 'commands'],
            ['ENTRYPOINT', [], 'entrypoint'],
            ['WORKDIR', false, 'workDir'],
            ['USER', [], 'user'],
            ['NETWORK_ALIASES', [false], 'networkAliases'],
            ['EXTRA_HOSTS', false, 'extraHosts'],
            ['MOUNTS', false, 'mounts'],
            ['VOLUMES_FROM', false, 'volumesFrom'],
            ['INSPECT_INTERVAL', INF, 'inspectInterval'],
            ['STOP_TIMEOUT', -2, 'stopTimeout'],
            ['STOP_SIGNAL', [], 'stopSignal'],
            ['STARTUP_TIMEOUT', -1, 'startupTimeout'],
            ['STARTUP_CONFLICT_RETRY_ATTEMPTS', 0, 'startupConflictRetryAttempts'],
            ['PORT_STRATEGY', [], 'portStrategy'],
            ['REUSE_MODE', false, 'reuseMode'],
            ['PULL_POLICY', [], 'pullPolicy'],
            ['NETWORK_MODE', [], 'networkMode'],
            ['SSH_PORT_FORWARD', [], 'sshPortForward'],
        ];
        foreach ($cases as $case) {
            $old = ValidationContainer::setDefault($case[0], $case[1]);
            try {
                $this->assertInvalid(function () use ($container, $case) {
                    $container->readSetting($case[2]);
                });
            } finally {
                ValidationContainer::setDefault($case[0], $old);
            }
        }
        ValidationContainer::setDefault('PORTS', ['80']);
        try {
            $this->assertSame([80], $container->readSetting('exposedPorts'));
            ValidationContainer::setDefault('PORTS', ['bad']);
            $this->assertInvalid(function () use ($container) {
                $container->readSetting('exposedPorts');
            });
        } finally {
            ValidationContainer::setDefault('PORTS', null);
        }
    }

    public function testInvalidPortListDoesNotReplaceExistingPorts()
    {
        $container = (new ValidationContainer('alpine'))->withPorts([1, '080', '65535']);
        $this->assertInvalid(function () use ($container) {
            $container->withPorts([443, false]);
        });
        $this->assertSame([1, 80, 65535], $container->readSetting('exposedPorts'));
    }

    public function testStrategiesAndValueObjectsValidatePublicInputs()
    {
        $cases = [
            function () { new GenericContainer([]); },
            function () { new GenericContainer(''); },
            function () { new StaticPortStrategy(false); },
            function () { (new HttpWaitStrategy())->withTimeoutSeconds(-1); },
            function () { (new HttpWaitStrategy())->withRetryInterval('1'); },
            function () { (new HttpWaitStrategy())->withExpectedResponseCode(99); },
            function () { (new PDOConnectWaitStrategy())->withDsn(new \stdClass()); },
            function () { (new PortStrategyProvider())->register('bad', new \stdClass()); },
            function () { HostToIp::fromArray(['hostname' => 'host']); },
            function () { VolumeFrom::fromArray(['name' => 'volume', 'mode' => false]); },
            function () { Mount::fromArray(['target' => '/data', 'readonly' => 'false']); },
        ];
        foreach ($cases as $case) {
            $this->assertInvalid($case);
        }
    }

    public function testEnumConstructorValidationDoesNotDependOnPhpAssertions()
    {
        try {
            new BindMode('invalid');
            $this->fail('Invalid enum value was accepted');
        } catch (InvalidFormatException $e) {
            $this->assertNotFalse(strpos($e->getMessage(), 'invalid'));
        }
    }

    protected function assertInvalid($call)
    {
        try {
            $call();
            $this->fail('Invalid input was accepted');
        } catch (\InvalidArgumentException $e) {
            $this->assertNotSame('', $e->getMessage());
        }
    }
}

class ValidationContainer extends GenericContainer
{
    public static function setDefault($name, $value)
    {
        $old = static::${$name};
        static::${$name} = $value;

        return $old;
    }

    public function readSetting($name)
    {
        return $this->{$name}();
    }
}
