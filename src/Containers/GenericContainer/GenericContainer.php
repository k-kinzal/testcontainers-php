<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Container;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckFailedException;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Environments;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\SSH\Tunnel;
use Testcontainers\Utility\WithLogger;

use function Testcontainers\ensure;

/**
 * GenericContainer is a generic implementation of docker container.
 */
class GenericContainer implements Container
{
    use AutoRemoveOnExitSetting;
    use EntrypointSetting;
    use EnvSetting;
    use GeneralSetting;
    use HostSetting;
    use LabelSetting;
    use MountSetting;
    use NetworkAliasSetting;
    use NetworkModeSetting;
    use PortSetting;
    use PrivilegeSetting;
    use PullPolicySetting;
    use ReuseModeSetting;
    use SSHPortForwardSetting;
    use StartupSetting;
    use StopSignalSetting;
    use StopTimeoutSetting;
    use UserSetting;
    use VolumesFromSetting;
    use WaitSetting;
    use WorkdirSetting;
    use WithLogger;

    /**
     * Unique session ID for the current PHP process.
     *
     * @var null|string
     */
    protected static $sessionId;

    /**
     * The Docker client.
     *
     * @var null|DockerClient
     */
    private $client;

    /**
     * @param null|string $image the image to be used for the container
     */
    public function __construct($image = null)
    {
        ensure($image === null || is_string($image), '$image must be null|string');
        ensure(static::$IMAGE === null || is_string(static::$IMAGE), 'static::$IMAGE must be null|string');

        if ($image === null && static::$IMAGE === null) {
            throw new \InvalidArgumentException('Unexpectedly image and static::$IMAGE are both null');
        }

        $this->image = $image !== null ? $image : static::$IMAGE;
    }

    /**
     * Set the Docker client.
     *
     * @param DockerClient $client the Docker client
     *
     * @return self
     */
    public function withDockerClient($client)
    {
        ensure($client instanceof DockerClient, '$client must be DockerClient');

        $this->client = $client;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidFormatException if the provided mode is not valid
     * @throws DockerException        if the Docker command fails
     */
    public function start()
    {
        $client = $this->client();

        $portStrategy = $this->portStrategy();

        $image = $this->image();
        $command = $this->command();
        $args = $this->args();
        $tcLabels = [
            'org.testcontainers' => 'true',
            'org.testcontainers.session-id' => self::generateSessionId(),
            'org.testcontainers.pid' => (string) getmypid(),
            'org.testcontainers.host' => (string) gethostname(),
        ];
        $options = [
            'addHost' => $this->extraHosts(),
            'detach' => true,
            'entrypoint' => $this->entrypoint(),
            'env' => $this->env(),
            'label' => array_merge($this->labels(), $tcLabels),
            'mount' => $this->mounts(),
            'name' => $this->name(),
            'network' => $this->networkMode(),
            'networkAlias' => $this->networkAliases(),
            'publish' => [],
            'pull' => $this->pullPolicy(),
            'privileged' => $this->privileged(),
            'rm' => $this->autoRemoveOnExit(),
            'user' => $this->user(),
            'volumesFrom' => $this->volumesFrom(),
            'workdir' => $this->workDir(),
        ];
        $this->logger()->debug('Starting container');

        $maxRetryAttempts = $this->startupConflictRetryAttempts();
        $retryCount = 0;
        $ports = [];
        $output = null;
        while ($retryCount < $maxRetryAttempts) {
            $ports = $this->ports();
            $options['publish'] = array_map(function ($containerPort, $hostPort) {
                return $hostPort.':'.$containerPort;
            }, array_keys($ports), array_values($ports));

            try {
                $startupTimeout = $this->startupTimeout();
                $runClient = $client->withLogger($this->logger());
                $originalTimeout = $runClient->getTimeout();
                if ($startupTimeout !== null) {
                    $runClient->withTimeout($startupTimeout);
                }
                $output = $runClient->run($image, $command, $args, $options);
                if ($startupTimeout !== null) {
                    $runClient->withTimeout($originalTimeout);
                }

                break; // Success, exit the retry loop
            } catch (PortAlreadyAllocatedException $e) {
                if ($portStrategy === null) {
                    throw $e;
                }
                $behavior = $portStrategy->conflictBehavior();
                if ($behavior->isRetry()) {
                    ++$retryCount;
                    if ($retryCount >= $maxRetryAttempts) {
                        $this->logger()->error('Maximum retry attempts reached for port allocation', [
                            'retryCount' => $retryCount,
                            'maxRetries' => $maxRetryAttempts,
                            'exception' => $e,
                        ]);

                        throw $e;
                    }
                    $this->logger()->debug('Port already allocated, retrying: '.$e->getMessage(), [
                        'exception' => $e,
                        'retryCount' => $retryCount,
                        'maxRetries' => $maxRetryAttempts,
                    ]);

                    continue; // Retry the loop
                }
                if ($behavior->isFail()) {
                    throw $e;
                }

                throw new \LogicException('Unknown conflict behavior: `'.(string) $behavior.'`', 0, $e);
            } catch (BindAddressAlreadyUseException $e) {
                if ($portStrategy === null) {
                    throw $e;
                }
                $behavior = $portStrategy->conflictBehavior();
                if ($behavior->isRetry()) {
                    ++$retryCount;
                    if ($retryCount >= $maxRetryAttempts) {
                        $this->logger()->error('Maximum retry attempts reached for bind address', [
                            'retryCount' => $retryCount,
                            'maxRetries' => $maxRetryAttempts,
                            'exception' => $e,
                        ]);

                        throw $e;
                    }
                    $this->logger()->debug('Bind address already in use, retrying: '.$e->getMessage(), [
                        'exception' => $e,
                        'retryCount' => $retryCount,
                        'maxRetries' => $maxRetryAttempts,
                    ]);

                    continue; // Retry the loop
                }
                if ($behavior->isFail()) {
                    throw $e;
                }

                throw new \LogicException('Unknown conflict behavior: `'.(string) $behavior.'`', 0, $e);
            } catch (ProcessTimedOutException $e) {
                throw new StartupCheckFailedException('container startup timed out', 0, $e);
            }
        }

        if (!$output instanceof DockerRunWithDetachOutput) {
            throw new \LogicException('Expected DockerRunWithDetachOutput');
        }

        $this->logger()->debug('Container started', [
            'containerId' => $output->getContainerId(),
        ]);

        $containerDef = [
            'containerId' => $output->getContainerId(),
            'labels' => $this->labels(),
            'ports' => $ports,
            'pull' => $this->pullPolicy(),
            'privileged' => $this->privileged(),
            'stopTimeout' => $this->stopTimeout(),
            'stopSignal' => $this->stopSignal(),
        ];
        $instance = new GenericContainerInstance($containerDef);
        $instance->setDockerClient($client);

        try {
            $startupCheckStrategy = $this->startupCheckStrategy($instance);
            if ($startupCheckStrategy) {
                $this->logger()->debug('Waiting for container to start', [
                    'strategy' => $startupCheckStrategy,
                ]);
                if ($startupCheckStrategy->withLogger($this->logger())->waitUntilStartupSuccessful($instance) === false) {
                    throw new StartupCheckFailedException();
                }
                $this->logger()->debug('Container started successfully');
            }

            if (count($ports) > 0) {
                $sshPortForward = $this->sshPortForward();
                if ($sshPortForward !== null) {
                    $port = $instance->getMappedPort(array_keys($ports)[0]);
                    if ($port !== null) {
                        $remoteHost = Environments::TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE();
                        if ($remoteHost === null) {
                            $remoteHost = '127.0.0.1';
                        }
                        $sshHost = isset($sshPortForward['sshHost']) ? $sshPortForward['sshHost'] : $instance->getHost();
                        $sshUser = isset($sshPortForward['sshUser']) ? $sshPortForward['sshUser'] : null;
                        $sshPort = isset($sshPortForward['sshPort']) ? $sshPortForward['sshPort'] : null;
                        $tunnel = (new Tunnel($port, $remoteHost, $port, $sshHost));
                        if ($sshUser !== null) {
                            $tunnel->withUser($sshUser);
                        }
                        if ($sshPort !== null) {
                            $tunnel->withSshPort($sshPort);
                        }
                        $this->logger()->debug('Opening SSH tunnel');
                        $session = $tunnel->open();
                        $instance->setData($session);
                        $this->logger()->debug('SSH tunnel opened', [
                            'session' => $session,
                        ]);
                    }
                }
            }

            $waitStrategy = $this->waitStrategy($instance);
            if ($waitStrategy) {
                $this->logger()->debug('Waiting for container to be ready', [
                    'strategy' => $waitStrategy,
                ]);
                $waitStrategy->withLogger($this->logger())->waitUntilReady($instance);
            }
        } catch (\Exception $e) {
            $this->logger()->debug('Container startup failed, stopping container', [
                'containerId' => $output->getContainerId(),
                'exception' => $e,
            ]);

            try {
                $instance->stop();
            } catch (\Exception $stopException) {
                $this->logger()->debug('Failed to stop container during cleanup', [
                    'exception' => $stopException,
                ]);
            }

            throw $e;
        }

        $this->logger()->debug('Container is ready');

        return $instance;
    }

    /**
     * Get the Docker client.
     *
     * @return DockerClient
     */
    protected function client()
    {
        return $this->client ?: DockerClientFactory::create();
    }

    /**
     * Generate a unique session ID for the current PHP process.
     *
     * @return string
     */
    protected static function generateSessionId()
    {
        if (self::$sessionId === null) {
            if (function_exists('random_bytes')) {
                self::$sessionId = bin2hex(random_bytes(16));
            } else {
                $pid = getmypid();
                self::$sessionId = md5(uniqid('', true) . ($pid !== false ? $pid : 0) . (string) microtime(true));
            }
        }

        return self::$sessionId;
    }
}
